<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'anxi_subscription_platform');
define('DB_USER', 'anxi_admin');
define('DB_PASS', 'delfin2015');

// Create uploads directory if not exists
if (!file_exists(__DIR__ . '/../uploads')) {
    mkdir(__DIR__ . '/../uploads', 0777, true);
}

// Application Settings
define('BASE_URL', 'http://localhost/video');
define('APP_NAME', 'Video Subscription Platform');
define('APP_ENV', 'development'); // 'development' or 'production'

// Security Settings
define('CONTENT_ENCRYPTION_KEY', bin2hex(random_bytes(32))); // Generate new key on first run
define('SESSION_LIFETIME', 3600); // 1 hour
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes
define('PASSWORD_MIN_LENGTH', 8);

// Content Protection Settings
define('MAX_FILE_SIZE', 104857600); // 100MB
define('ALLOWED_VIDEO_TYPES', ['video/mp4', 'video/webm']);
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp']);
define('WATERMARK_TEXT', 'Protected Content - User: %s');
define('VIDEO_CHUNK_SIZE', 8192); // 8KB chunks for video streaming

// API Keys
define('GEMINI_API_KEY', 'your_gemini_api_key_here');
define('CRYPTO_API_KEY', 'your_crypto_payment_api_key_here');
define('CRYPTO_MERCHANT_ID', 'your_crypto_merchant_id_here');

// Rate Limiting
define('RATE_LIMIT_WINDOW', 3600); // 1 hour
define('API_RATE_LIMIT', 1000); // requests per window
define('CHAT_RATE_LIMIT', 60); // messages per 5 minutes

// Subscription Plans
define('MONTHLY_SUBSCRIPTION_PRICE', 9.99);
define('QUARTERLY_SUBSCRIPTION_PRICE', 24.99);
define('ANNUAL_SUBSCRIPTION_PRICE', 89.99);

// Error Reporting
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Security Headers
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');
header("Content-Security-Policy: default-src 'self' https://cdn.tailwindcss.com; img-src 'self' data:; media-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com;");

// Timezone
date_default_timezone_set('UTC');