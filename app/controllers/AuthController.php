<?php
class AuthController extends Controller {
    public function index() {
        // Redirect to login if not authenticated
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit();
        }
        // Redirect to dashboard if authenticated
        header('Location: ' . BASE_URL . '/content');
        exit();
    }    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                $password = $_POST['password'] ?? '';
                
                if (empty($email) || empty($password)) {
                    $_SESSION['error'] = 'Email and password are required';
                    $this->view('auth/login', ['error' => $_SESSION['error']]);
                    return;
                }

                // Debug log
                error_log("Login attempt for email: " . $email);

                // Rate limiting check
                if (Utils::checkRateLimit($_SERVER['REMOTE_ADDR'], 'login', MAX_LOGIN_ATTEMPTS, LOGIN_LOCKOUT_TIME)) {
                    $_SESSION['error'] = 'Too many login attempts. Please try again later.';
                    $this->view('auth/login', ['error' => $_SESSION['error']]);
                    return;
                }
                
                $result = $this->db->query("SELECT * FROM users WHERE email = ? LIMIT 1", [$email]);
                $user = !empty($result) ? $result[0] : null;
                
                if ($user && password_verify($password, $user->password)) {
                    // Clear any previous errors
                    unset($_SESSION['error']);
                    
                    // Login successful
                    $_SESSION['user_id'] = $user->id;
                    $_SESSION['is_admin'] = $user->is_admin;
                    $_SESSION['email'] = $user->email;
                    
                    // Log successful login
                    error_log("Successful login for user: " . $user->email);
                    
                    // Generate new session ID to prevent session fixation
                    session_regenerate_id(true);
                    
                    // Set remember me cookie if requested
                    if (isset($_POST['remember-me'])) {
                        try {
                            $token = bin2hex(random_bytes(32));
                            $this->db->query(
                                "UPDATE users SET remember_token = ?, updated_at = NOW() WHERE id = ?",
                                [$token, $user->id]
                            );
                            setcookie('remember_token', $token, time() + 30*24*60*60, '/', '', true, true);
                        } catch (Exception $e) {
                            error_log("Error setting remember token: " . $e->getMessage());
                        }
                    }
                    
                    header('Location: ' . BASE_URL . ($user->is_admin ? '/admin' : '/content'));
                    exit();
                } else {
                    // Log failed attempt
                    Utils::incrementRateLimit($_SERVER['REMOTE_ADDR'], 'login');
                    $_SESSION['error'] = 'Invalid email or password';
                }
            } catch (Exception $e) {
                error_log("Login error: " . $e->getMessage());
                $_SESSION['error'] = 'An error occurred during login. Please try again.';
            }
        }
        
        $this->view('auth/login', [
            'error' => $_SESSION['error'] ?? null
        ]);
        unset($_SESSION['error']);
    }
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                $password = $_POST['password'] ?? '';
                $confirmPassword = $_POST['confirm_password'] ?? '';
                
                // Debug log
                error_log("Registration attempt for email: " . $email);
                
                // Validation
                $errors = [];
                
                if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = 'Valid email address is required';
                }
                
                // Enhanced password validation
                if (strlen($password) < PASSWORD_MIN_LENGTH) {
                    $errors[] = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters';
                }
                
                if (!preg_match('/[A-Z]/', $password)) {
                    $errors[] = 'Password must contain at least one uppercase letter';
                }
                
                if (!preg_match('/[a-z]/', $password)) {
                    $errors[] = 'Password must contain at least one lowercase letter';
                }
                
                if (!preg_match('/[0-9]/', $password)) {
                    $errors[] = 'Password must contain at least one number';
                }
                
                if ($password !== $confirmPassword) {
                    $errors[] = 'Passwords do not match';
                }
                
                // Check if email already exists
                $existingUser = $this->db->query("SELECT id FROM users WHERE email = ? LIMIT 1", [$email]);
                if (!empty($existingUser)) {
                    error_log("Registration failed: Email already exists - " . $email);
                    $errors[] = 'Email address is already registered';
                }

                if (empty($errors)) {
                    try {
                        $this->db->beginTransaction();
                        
                        // Hash password with better cost factor
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
                        
                        // Create user with additional security fields
                        $this->db->query(
                            "INSERT INTO users (email, password, created_at, email_verified, last_login, status) 
                             VALUES (?, ?, NOW(), 0, NULL, 'active')",
                            [$email, $hashedPassword]
                        );
                        
                        $this->db->commit();
                        
                        $_SESSION['success'] = 'Registration successful! Please login.';
                        header('Location: ' . BASE_URL . '/auth/login');
                        exit();
                        
                    } catch (Exception $e) {
                        $this->db->rollback();
                        $errors[] = 'Registration failed. Please try again.';
                    }
                }
                
                if (!empty($errors)) {
                    $_SESSION['errors'] = $errors;
                    $this->view('auth/register', ['email' => $email]);
                    return;
                }
            } catch (Exception $e) {
                error_log("Registration error: " . $e->getMessage());
                $_SESSION['errors'] = ['An unexpected error occurred. Please try again.'];
                $this->view('auth/register', ['email' => $email]);
                return;
            }
        }
        
        // Display registration form
        $this->view('auth/register', [
            'errors' => $_SESSION['errors'] ?? [],
            'email' => $_SESSION['old_email'] ?? ''
        ]);
        
        unset($_SESSION['errors'], $_SESSION['old_email']);
    }

    public function subscribe() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            return;
        }

        // Check if user already has an active subscription
        if ($this->isSubscribed()) {
            header('Location: ' . BASE_URL . '/content');
            return;
        }

        $this->view('auth/subscribe');
    }

    public function processPayment() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/auth/subscribe');
            return;
        }

        $plan = filter_input(INPUT_POST, 'plan', FILTER_SANITIZE_STRING);
        $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
        $paymentMethod = filter_input(INPUT_POST, 'payment_method', FILTER_SANITIZE_STRING);

        try {
            $paymentId = null;
            
            if ($paymentMethod === 'crypto') {
                $cryptoCurrency = filter_input(INPUT_POST, 'crypto_currency', FILTER_SANITIZE_STRING);
                $paymentId = $this->processCryptoPayment($amount, $cryptoCurrency);
            } else if ($paymentMethod === 'card') {
                $cardNumber = filter_input(INPUT_POST, 'card_number', FILTER_SANITIZE_STRING);
                $cardExpiry = filter_input(INPUT_POST, 'card_expiry', FILTER_SANITIZE_STRING);
                $cardCvc = filter_input(INPUT_POST, 'card_cvc', FILTER_SANITIZE_STRING);
                $paymentId = $this->processCardPayment($amount, $cardNumber, $cardExpiry, $cardCvc);
            }

            if ($paymentId) {
                // Calculate subscription end date based on plan
                $endDate = new DateTime();
                switch ($plan) {
                    case 'monthly':
                        $endDate->modify('+1 month');
                        break;
                    case 'quarterly':
                        $endDate->modify('+3 months');
                        break;
                    case 'annual':
                        $endDate->modify('+1 year');
                        break;
                }

                // Start transaction
                $this->db->startTransaction();

                // Create subscription record
                $this->db->query(
                    "INSERT INTO subscriptions (user_id, plan_type, subscription_end, payment_id, amount) 
                     VALUES (?, ?, ?, ?, ?)",
                    [
                        $_SESSION['user_id'],
                        $plan,
                        $endDate->format('Y-m-d H:i:s'),
                        $paymentId,
                        $amount
                    ]
                );

                // Log the successful payment
                Utils::logSecurityEvent($_SESSION['user_id'], 'subscription_created', [
                    'plan' => $plan,
                    'payment_method' => $paymentMethod,
                    'payment_id' => $paymentId
                ]);

                $this->db->commit();
                header('Location: ' . BASE_URL . '/content');
            } else {
                throw new Exception('Payment processing failed');
            }
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Payment Error: " . $e->getMessage());
            $_SESSION['payment_error'] = 'Payment processing failed. Please try again.';
            header('Location: ' . BASE_URL . '/auth/subscribe');
        }
    }

    private function processCryptoPayment($amount, $currency) {
        // Initialize crypto payment gateway
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.cryptopayment.com/v1/create",  // Replace with actual crypto payment gateway
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "X-API-Key: " . CRYPTO_API_KEY
            ],
            CURLOPT_POSTFIELDS => json_encode([
                'amount' => $amount,
                'currency' => $currency,
                'merchant_id' => CRYPTO_MERCHANT_ID,
                'callback_url' => BASE_URL . '/webhook/crypto'
            ])
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            throw new Exception("Crypto payment gateway error: " . $err);
        }

        $result = json_decode($response, true);
        if (isset($result['payment_id'])) {
            return $result['payment_id'];
        }

        throw new Exception("Invalid response from crypto payment gateway");
    }

    private function processCardPayment($amount, $cardNumber, $expiry, $cvc) {
        // Implement card payment processing here
        // This is just a placeholder - replace with actual payment gateway integration
        return 'card_' . uniqid();
    }

    public function webhookCrypto() {
        // Handle crypto payment webhook
        $payload = file_get_contents('php://input');
        $signature = $_SERVER['HTTP_X_SIGNATURE'] ?? '';

        // Verify webhook signature
        if (!$this->verifyWebhookSignature($payload, $signature)) {
            header('HTTP/1.1 401 Unauthorized');
            exit;
        }

        $data = json_decode($payload, true);
        if ($data['status'] === 'completed') {
            // Update subscription status
            $this->db->query(
                "UPDATE subscriptions SET status = 'active' WHERE payment_id = ?",
                [$data['payment_id']]
            );
        }

        header('HTTP/1.1 200 OK');
    }

    private function verifyWebhookSignature($payload, $signature) {
        // Implement webhook signature verification
        // This should match your crypto payment gateway's verification method
        $expectedSignature = hash_hmac('sha256', $payload, CRYPTO_API_KEY);
        return hash_equals($expectedSignature, $signature);
    }

    public function logout() {
        // Clean up session
        session_unset();
        session_destroy();
        
        // Remove remember me cookie if exists
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
        }
        
        // Redirect to login
        header('Location: ' . BASE_URL . '/auth/login');
        exit();
    }

    // Method to handle remember me functionality
    private function handleRememberMe() {
        if (isset($_COOKIE['remember_token'])) {
            $token = $_COOKIE['remember_token'];
            $user = $this->db->query(
                "SELECT * FROM users WHERE remember_token = ? LIMIT 1", 
                [$token]
            );
            
            if ($user) {
                $_SESSION['user_id'] = $user->id;
                $_SESSION['is_admin'] = $user->is_admin;
                $_SESSION['email'] = $user->email;
                
                // Generate new token for security
                $newToken = bin2hex(random_bytes(32));
                $this->db->query(
                    "UPDATE users SET remember_token = ? WHERE id = ?",
                    [$newToken, $user->id]
                );
                setcookie('remember_token', $newToken, time() + 30*24*60*60, '/', '', true, true);
            }
        }
    }
}