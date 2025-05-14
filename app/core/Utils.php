<?php
class Utils {
    private static $redis;

    public static function initRedis() {
        if (!self::$redis) {
            self::$redis = new Redis();
            self::$redis->connect('127.0.0.1', 6379);
        }
    }    public static function checkRateLimit($userId, $action, $limit, $window) {
        $now = time();
        $key = "rate_limit_{$action}_{$userId}";
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['count' => 1, 'first_attempt' => $now];
            return false;
        }
        
        $timeElapsed = $now - $_SESSION[$key]['first_attempt'];
        
        if ($timeElapsed > $window) {
            $_SESSION[$key] = ['count' => 1, 'first_attempt' => $now];
            return false;
        }
        
        if ($_SESSION[$key]['count'] >= $limit) {
            return true;
        }
        
        $_SESSION[$key]['count']++;
        return false;
    }

    /**
     * Add watermark to video
     */
    public static function addWatermark($videoPath, $userId) {
        $outputPath = pathinfo($videoPath, PATHINFO_DIRNAME) . '/watermarked_' . 
                     pathinfo($videoPath, PATHINFO_BASENAME);
        
        $watermark = self::generateWatermarkText($userId);
        $ffmpegCmd = sprintf(
            'ffmpeg -i %s -vf "drawtext=text=\'%s\':x=(w-text_w)/2:y=(h-text_h)/2:' .
            'fontsize=24:fontcolor=white@0.5:shadowcolor=black@0.5:shadowx=2:shadowy=2" ' .
            '-codec:a copy %s',
            escapeshellarg($videoPath),
            $watermark,
            escapeshellarg($outputPath)
        );
        
        exec($ffmpegCmd, $output, $returnVal);
        return $returnVal === 0 ? $outputPath : false;
    }

    /**
     * Sanitize filename by removing unwanted characters
     */
    public static function sanitizeFilename($filename) {
        // Remove any character that isn't a word character, dash, or dot
        $filename = preg_replace('/[^\w\-\.]/', '', $filename);
        // Ensure the filename doesn't start with a dot or dash
        $filename = ltrim($filename, '.-');
        return $filename;
    }

    /**
     * Validate token format
     */
    public static function validateToken($token) {
        return preg_match('/^[a-f0-9]{64}$/', $token) === 1;
    }

    /**
     * Generate a nonce (number used once) for CSRF protection
     */
    public static function generateNonce() {
        return bin2hex(random_bytes(32));
    }

    /**
     * Hash password using Argon2id
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3
        ]);
    }

    /**
     * Verify hashed password
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Validate email format
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate password against complexity requirements
     */
    public static function validatePassword($password) {
        // At least 8 characters
        if (strlen($password) < PASSWORD_MIN_LENGTH) return false;
        
        // Must contain at least one uppercase letter
        if (!preg_match('/[A-Z]/', $password)) return false;
        
        // Must contain at least one lowercase letter
        if (!preg_match('/[a-z]/', $password)) return false;
        
        // Must contain at least one number
        if (!preg_match('/[0-9]/', $password)) return false;
        
        // Must contain at least one special character
        if (!preg_match('/[^A-Za-z0-9]/', $password)) return false;
        
        return true;
    }

    /**
     * Securely compare two strings to prevent timing attacks
     */
    public static function secureCompare($a, $b) {
        if (function_exists('hash_equals')) {
            return hash_equals($a, $b);
        }
        
        if (strlen($a) !== strlen($b)) {
            return false;
        }
        
        $result = 0;
        for ($i = 0; $i < strlen($a); $i++) {
            $result |= ord($a[$i]) ^ ord($b[$i]);
        }
        return $result === 0;
    }

    /**
     * Detect MIME type of a file
     */
    public static function detectMimeType($file) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file);
        finfo_close($finfo);
        return $mimeType;
    }

    /**
     * Format bytes into a human-readable string
     */
    public static function formatBytes($bytes) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Generate a random string of given length
     */
    public static function generateRandomString($length = 32) {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Get client IP address, considering proxies
     */
    public static function getClientIP() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        
        // Validate IP address format
        if (filter_var($ipaddress, FILTER_VALIDATE_IP)) {
            return $ipaddress;
        }
        return 'UNKNOWN';
    }

    /**
     * Check if the subscription plan is valid
     */
    public static function isValidSubscriptionPlan($plan) {
        return in_array($plan, ['monthly', 'quarterly', 'annual']);
    }

    /**
     * Get the price of the subscription plan
     */
    public static function getPlanPrice($plan) {
        switch ($plan) {
            case 'monthly':
                return MONTHLY_SUBSCRIPTION_PRICE;
            case 'quarterly':
                return QUARTERLY_SUBSCRIPTION_PRICE;
            case 'annual':
                return ANNUAL_SUBSCRIPTION_PRICE;
            default:
                return 0;
        }
    }

    /**
     * Get the duration of the subscription plan
     */
    public static function getPlanDuration($plan) {
        switch ($plan) {
            case 'monthly':
                return '+1 month';
            case 'quarterly':
                return '+3 months';
            case 'annual':
                return '+1 year';
            default:
                return '';
        }
    }

    /**
     * Generate a token
     */
    public static function generateToken($length = 32) {
        return bin2hex(random_bytes($length));
    }

    /**
     * Generate watermark text
     */
    public static function generateWatermarkText($userId) {
        return sprintf('User ID: %d - %s', $userId, date('Y-m-d H:i:s'));
    }

    /**
     * Validate subscription status
     */
    public static function validateSubscriptionStatus($userId) {
        $db = Database::getInstance();
        $subscription = $db->query(
            "SELECT status, expires_at FROM subscriptions 
             WHERE user_id = ? ORDER BY expires_at DESC LIMIT 1",
            [$userId]
        );
        
        if (!$subscription) {
            return false;
        }
        
        return $subscription[0]->status === 'active' && 
               strtotime($subscription[0]->expires_at) > time();
    }

    /**
     * Get API key
     */
    public static function generateApiKey() {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * Validate API key
     */
    public static function validateApiKey($apiKey) {
        $db = Database::getInstance();
        $key = $db->query(
            "SELECT id FROM api_keys WHERE key_value = ? AND active = 1 
             AND (expires_at IS NULL OR expires_at > NOW())",
            [$apiKey]
        );
        return !empty($key);
    }
}