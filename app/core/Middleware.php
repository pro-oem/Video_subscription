<?php
class Middleware {
    private static $instance = null;
    private $db;
    private $accessLogger;
    
    private function __construct() {
        $this->db = Database::getInstance();
        $this->accessLogger = AccessLogger::getInstance();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function authenticate() {
        session_start();
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            if (isset($_COOKIE['remember_token'])) {
                $this->attemptTokenLogin();
            } else {
                $this->redirectToLogin();
            }
        }
        
        // Validate session
        if (!$this->validateSession()) {
            $this->destroySession();
            $this->redirectToLogin();
        }
        
        // Check for session hijacking
        if (!$this->validateSessionFingerprint()) {
            $this->accessLogger->logAccess(
                $_SESSION['user_id'],
                0,
                'session_validation',
                'failed',
                'Potential session hijacking detected'
            );
            $this->destroySession();
            $this->redirectToLogin();
        }
        
        // Update last activity
        $_SESSION['last_activity'] = time();
    }
    
    public function requireSubscription() {
        if (!isset($_SESSION['subscription_status']) || $_SESSION['subscription_status'] !== 'active') {
            header('Location: /auth/subscribe.php');
            exit;
        }
    }
    
    public function requireAdmin() {
        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            $this->accessLogger->logAccess(
                $_SESSION['user_id'] ?? 0,
                0,
                'admin_access',
                'failed',
                'Unauthorized admin access attempt'
            );
            header('Location: /errors/error.php?code=403');
            exit;
        }
    }
    
    public function checkRateLimit($action = 'default') {
        if (!Utils::checkRateLimit($_SESSION['user_id'], $action, RATE_LIMIT, RATE_LIMIT_WINDOW)) {
            $this->accessLogger->logAccess(
                $_SESSION['user_id'],
                0,
                'rate_limit',
                'failed',
                "Rate limit exceeded for action: {$action}"
            );
            header('Location: /errors/error.php?code=429');
            exit;
        }
    }
    
    public function csrfProtection() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !$this->validateCsrfToken($_POST['csrf_token'])) {
                $this->accessLogger->logAccess(
                    $_SESSION['user_id'] ?? 0,
                    0,
                    'csrf_validation',
                    'failed',
                    'Invalid CSRF token'
                );
                header('Location: /errors/error.php?code=400');
                exit;
            }
        }
    }
    
    public function generateCsrfToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    private function validateCsrfToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    private function validateSession() {
        // Check session expiry
        if (isset($_SESSION['last_activity']) && 
            (time() - $_SESSION['last_activity'] > SESSION_LIFETIME)) {
            return false;
        }
        
        // Verify user still exists and is active
        $user = $this->db->query(
            "SELECT status FROM users WHERE id = ?",
            [$_SESSION['user_id']]
        );
        
        return $user && $user[0]->status === 'active';
    }
    
    private function validateSessionFingerprint() {
        if (!isset($_SESSION['fingerprint'])) {
            $_SESSION['fingerprint'] = $this->generateFingerprint();
            return true;
        }
        
        return hash_equals($_SESSION['fingerprint'], $this->generateFingerprint());
    }
    
    private function generateFingerprint() {
        return hash('sha256', 
            $_SERVER['HTTP_USER_AGENT'] . 
            (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'])
        );
    }
    
    private function attemptTokenLogin() {
        $token = $_COOKIE['remember_token'];
        if (!Utils::validateToken($token)) {
            return;
        }
        
        $user = $this->db->query(
            "SELECT u.* FROM users u 
             JOIN remember_tokens rt ON u.id = rt.user_id 
             WHERE rt.token = ? AND rt.expires_at > NOW()",
            [$token]
        );
        
        if ($user) {
            $_SESSION['user_id'] = $user[0]->id;
            $_SESSION['username'] = $user[0]->username;
            $_SESSION['is_admin'] = $user[0]->is_admin;
            $_SESSION['subscription_status'] = $user[0]->subscription_status;
            $_SESSION['fingerprint'] = $this->generateFingerprint();
            $_SESSION['last_activity'] = time();
        }
    }
    
    private function destroySession() {
        session_unset();
        session_destroy();
        setcookie('remember_token', '', time() - 3600, '/');
    }
    
    private function redirectToLogin() {
        header('Location: /auth/login.php');
        exit;
    }
    
    public function validateContentAccess($contentId) {
        $content = $this->db->query(
            "SELECT access_level, owner_id FROM content WHERE id = ?",
            [$contentId]
        );
        
        if (!$content) {
            return false;
        }
        
        // Owner always has access
        if ($content[0]->owner_id === $_SESSION['user_id']) {
            return true;
        }
        
        // Check access level requirements
        switch ($content[0]->access_level) {
            case 'public':
                return true;
            case 'registered':
                return isset($_SESSION['user_id']);
            case 'premium':
                return isset($_SESSION['subscription_status']) && 
                       $_SESSION['subscription_status'] === 'active';
            case 'private':
                return false;
            default:
                return false;
        }
    }
    
    public function logRequest() {
        $this->accessLogger->logAccess(
            $_SESSION['user_id'] ?? 0,
            0,
            $_SERVER['REQUEST_URI'],
            'success',
            $_SERVER['REQUEST_METHOD']
        );
    }
}