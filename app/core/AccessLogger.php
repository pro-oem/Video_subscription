<?php
class AccessLogger {
    private $db;
    private static $instance = null;
    
    private function __construct() {
        $this->db = Database::getInstance();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function logAccess($userId, $contentId, $action, $status, $additionalInfo = '') {
        $ip = Utils::getClientIP();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        
        $this->db->query(
            "INSERT INTO access_logs (user_id, content_id, action, status, ip_address, user_agent, additional_info) 
             VALUES (?, ?, ?, ?, ?, ?, ?)",
            [$userId, $contentId, $action, $status, $ip, $userAgent, $additionalInfo]
        );
    }
    
    public function checkSuspiciousActivity($userId, $timeWindow = 3600) {
        // Check for multiple failed access attempts
        $failedAttempts = $this->db->query(
            "SELECT COUNT(*) as count 
             FROM access_logs 
             WHERE user_id = ? 
             AND status = 'failed' 
             AND created_at >= NOW() - INTERVAL ? SECOND",
            [$userId, $timeWindow]
        );
        
        return $failedAttempts->count >= 5;
    }
    
    public function getContentAccessHistory($contentId, $limit = 100) {
        return $this->db->query(
            "SELECT al.*, u.username 
             FROM access_logs al 
             JOIN users u ON al.user_id = u.id 
             WHERE al.content_id = ? 
             ORDER BY al.created_at DESC 
             LIMIT ?",
            [$contentId, $limit]
        );
    }
    
    public function getUserAccessHistory($userId, $limit = 100) {
        return $this->db->query(
            "SELECT al.*, c.title as content_title 
             FROM access_logs al 
             JOIN content c ON al.content_id = c.id 
             WHERE al.user_id = ? 
             ORDER BY al.created_at DESC 
             LIMIT ?",
            [$userId, $limit]
        );
    }
    
    public function getRecentFailedAttempts($minutes = 60) {
        return $this->db->query(
            "SELECT al.*, u.username, c.title as content_title 
             FROM access_logs al 
             JOIN users u ON al.user_id = u.id 
             JOIN content c ON al.content_id = c.id 
             WHERE al.status = 'failed' 
             AND al.created_at >= NOW() - INTERVAL ? MINUTE 
             ORDER BY al.created_at DESC",
            [$minutes]
        );
    }
    
    public function flagSuspiciousUser($userId, $reason) {
        $this->db->query(
            "INSERT INTO suspicious_activity (user_id, reason) VALUES (?, ?)",
            [$userId, $reason]
        );
        
        // Notify administrators
        $this->notifyAdmins($userId, $reason);
    }
    
    public function getAggregatedStats($timeWindow = '24 HOUR') {
        return $this->db->query(
            "SELECT 
                COUNT(*) as total_accesses,
                COUNT(CASE WHEN status = 'failed' THEN 1 END) as failed_attempts,
                COUNT(DISTINCT user_id) as unique_users,
                COUNT(DISTINCT content_id) as unique_content
             FROM access_logs 
             WHERE created_at >= NOW() - INTERVAL ?",
            [$timeWindow]
        );
    }
    
    private function notifyAdmins($userId, $reason) {
        $admins = $this->db->query("SELECT email FROM users WHERE is_admin = 1");
        $user = $this->db->query("SELECT username FROM users WHERE id = ?", [$userId]);
        
        $subject = "Suspicious Activity Detected";
        $message = sprintf(
            "Suspicious activity detected for user %s (ID: %d)\nReason: %s\nTime: %s\nIP: %s",
            $user->username,
            $userId,
            $reason,
            date('Y-m-d H:i:s'),
            Utils::getClientIP()
        );
        
        foreach ($admins as $admin) {
            mail($admin->email, $subject, $message);
        }
    }
    
    public function logAPIRequest($userId, $endpoint, $method, $status, $response = '') {
        $this->db->query(
            "INSERT INTO api_logs (user_id, endpoint, method, status, response, ip_address) 
             VALUES (?, ?, ?, ?, ?, ?)",
            [$userId, $endpoint, $method, $status, $response, Utils::getClientIP()]
        );
    }
    
    public function checkAPIRateLimit($userId) {
        return Utils::checkRateLimit($userId, 'api', API_RATE_LIMIT, RATE_LIMIT_WINDOW);
    }
    
    public function purgeOldLogs($days = 30) {
        $this->db->query(
            "DELETE FROM access_logs WHERE created_at < NOW() - INTERVAL ? DAY",
            [$days]
        );
        
        $this->db->query(
            "DELETE FROM api_logs WHERE created_at < NOW() - INTERVAL ? DAY",
            [$days]
        );
    }
}