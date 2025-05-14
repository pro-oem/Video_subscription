<?php
class Controller {
    protected $db;
    protected $userId;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->userId = $_SESSION['user_id'] ?? null;
    }    protected function view($view, $data = []) {
        try {
            // Convert the view path to absolute path
            $viewPath = dirname(dirname(__DIR__)) . '/app/views/' . $view . '.php';
            
            // Add CSRF token to all views
            if (!isset($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
            $data['csrf_token'] = $_SESSION['csrf_token'];
            
            // Start output buffering for view content
            ob_start();
            
            // Extract data to make it available in view
            if (!empty($data)) {
                extract($data);
            }
            
            // Include the view
            if (!file_exists($viewPath)) {
                throw new Exception("View file not found: {$viewPath}");
            }
            
            require $viewPath;
            
            // Get the content from the view and store it for layout
            $data['content'] = ob_get_clean();
            
            if (empty(trim($content))) {
                error_log("View generated empty content: " . $viewPath . ". Data: " . print_r($data, true));
                throw new Exception("View generated empty content. Please check the error log for details.");
            }            if (empty(trim($data['content']))) {
                error_log("View generated empty content: " . $viewPath . ". Data: " . print_r($data, true));
                throw new Exception("View generated empty content. Please check the error log for details.");
            }
            
            // Start new buffer for layout
            ob_start();
            
            // Include the layout with the captured content
            $layoutPath = dirname(dirname(__DIR__)) . '/app/views/layouts/main.php';
            if (!file_exists($layoutPath)) {
                throw new Exception("Layout file not found: {$layoutPath}");
            }
            
            // Extract data again for layout (includes content)
            extract($data);
            
            require $layoutPath;
            
            // Output everything
            echo ob_get_clean();
            
        } catch (Exception $e) {
            error_log("View Error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            // Include error view directly without layout
            include dirname(dirname(__DIR__)) . '/app/views/errors/error.php';
        }
    }

    protected function isSubscribed() {
        if (!$this->userId) return false;
        
        $subscription = $this->db->query(
            "SELECT * FROM subscriptions 
             WHERE user_id = ? AND subscription_end > NOW() 
             ORDER BY subscription_end DESC LIMIT 1",
            [$this->userId]
        );
        
        return !empty($subscription);
    }

    protected function preventContentCopy() {
        header('X-Frame-Options: DENY');
        header('Content-Security-Policy: default-src \'self\'; media-src \'self\'; img-src \'self\' data:; script-src \'self\' \'unsafe-inline\'');
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: same-origin');
    }

    protected function requireAuth() {
        if (!$this->userId || !isset($_SESSION['user_id'])) {
            error_log("Auth required - No user ID in session");
            if ($this->isApiRequest()) {
                $this->jsonResponse(['error' => 'Unauthorized'], 401);
            } else {
                $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
                header('Location: ' . BASE_URL . '/auth/login');
                exit();
            }
        }
    }

    protected function requireAdmin() {
        $this->requireAuth();
        
        try {
            $user = $this->db->query("SELECT is_admin FROM users WHERE id = ? LIMIT 1", [$this->userId]);
            
            if (!$user || !$user->is_admin) {
                error_log("Admin access denied for user ID: " . $this->userId);
                if ($this->isApiRequest()) {
                    $this->jsonResponse(['error' => 'Forbidden'], 403);
                } else {
                    $_SESSION['error'] = 'Access denied. Admin privileges required.';
                    header('Location: ' . BASE_URL . '/auth/login');
                    exit();
                }
            }
        } catch (Exception $e) {
            error_log("Admin check error: " . $e->getMessage());
            if ($this->isApiRequest()) {
                $this->jsonResponse(['error' => 'Internal Server Error'], 500);
            } else {
                $_SESSION['error'] = 'An error occurred. Please try again.';
                header('Location: ' . BASE_URL . '/auth/login');
                exit();
            }
        }
    }

    protected function requireSubscription() {
        $this->requireAuth();
        
        if (!$this->isSubscribed()) {
            if ($this->isApiRequest()) {
                $this->jsonResponse(['error' => 'Subscription required'], 403);
            } else {
                header('Location: ' . BASE_URL . '/auth/subscribe');
                exit();
            }
        }
    }

    protected function verifyCSRF() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                if ($this->isApiRequest()) {
                    $this->jsonResponse(['error' => 'Invalid CSRF token'], 403);
                } else {
                    $this->view('errors/error', [
                        'title' => 'Security Error',
                        'message' => 'Invalid security token. Please try again.'
                    ]);
                    exit();
                }
            }
        }
    }

    protected function isApiRequest() {
        return (
            isset($_SERVER['HTTP_ACCEPT']) && 
            strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false
        ) || (
            isset($_SERVER['CONTENT_TYPE']) && 
            strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false
        );
    }

    protected function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    protected function sanitizeInput($input, $type = 'string') {
        switch ($type) {
            case 'email':
                return filter_var($input, FILTER_SANITIZE_EMAIL);
            case 'url':
                return filter_var($input, FILTER_SANITIZE_URL);
            case 'int':
                return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
            case 'float':
                return filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            default:
                return filter_var($input, FILTER_SANITIZE_STRING);
        }
    }

    protected function validateFile($file, $allowedTypes, $maxSize = 104857600) { // 100MB default
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('File upload failed');
        }

        if ($file['size'] > $maxSize) {
            throw new Exception('File size exceeds limit');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            throw new Exception('Invalid file type');
        }

        return true;
    }

    protected function generateSecureFilename($originalName) {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        return uniqid() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
    }
}