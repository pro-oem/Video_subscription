<?php
// Initialize custom error handler
require_once 'app/core/ErrorHandler.php';
ErrorHandler::initialize();

// Session security settings must be set before starting the session
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.gc_maxlifetime', 3600);

// Load configuration first to get constants
require_once 'config/config.php';

// In development, don't require HTTPS
if (APP_ENV === 'production' || (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')) {
    ini_set('session.cookie_secure', 1);
}

session_start();
require_once 'app/core/Router.php';
require_once 'app/core/Controller.php';
require_once 'app/core/Database.php';
require_once 'app/core/Utils.php';
require_once 'app/core/AccessLogger.php';
require_once 'app/core/Middleware.php';

// Set security headers with proper CSP
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');

// Setup Content Security Policy
$csp = array(
    "default-src" => ["'self'"],
    "style-src" => ["'self'", "'unsafe-inline'", "https://cdn.tailwindcss.com"],
    "script-src" => ["'self'", "'unsafe-inline'", "https://cdn.tailwindcss.com"],
    "img-src" => ["'self'", "data:", "blob:"],
    "media-src" => ["'self'", "blob:"],
    "connect-src" => ["'self'"],
    "font-src" => ["'self'", "data:"],
    "frame-ancestors" => ["'none'"]
);

$cspHeader = "";
foreach ($csp as $directive => $values) {
    $cspHeader .= $directive . " " . implode(" ", $values) . "; ";
}

header("Content-Security-Policy: " . trim($cspHeader));

// Handle CORS for API requests
if (isset($_SERVER['HTTP_ORIGIN'])) {
    $allowedOrigins = [
        'http://localhost',
        'http://localhost:3000',
        'http://127.0.0.1'
    ];
    
    if (in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
        header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
        header("Access-Control-Allow-Credentials: true");
        
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
            header("Access-Control-Allow-Headers: Content-Type, Authorization");
            header("Access-Control-Max-Age: 3600");
            exit(0);
        }
    }
}

// Initialize router and handle the request
try {
    $router = new Router();
    $router->run();
} catch (Exception $e) {
    $errorMessage = $e->getMessage();
    $errorCode = $e->getCode() ?: 500;
    
    // Log detailed error for debugging
    error_log(sprintf(
        "Error: %s\nFile: %s\nLine: %d\nTrace:\n%s",
        $errorMessage,
        $e->getFile(),
        $e->getLine(),
        $e->getTraceAsString()
    ));
    
    // Return appropriate error response
    http_response_code($errorCode);
    
    if (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
        header('Content-Type: application/json');
        echo json_encode([
            'error' => APP_ENV === 'development' ? $errorMessage : 'An error occurred',
            'code' => $errorCode
        ]);
    } else {
        include 'app/views/errors/error.php';
    }
}