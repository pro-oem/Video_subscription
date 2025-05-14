<?php

class ErrorHandler {
    public static function initialize() {
        error_reporting(E_ALL);
        ini_set('display_errors', 0);
        ini_set('log_errors', 1);
        ini_set('error_log', __DIR__ . '/../../error.log');
        
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
    }
    
    public static function handleError($errno, $errstr, $errfile, $errline) {
        if (!(error_reporting() & $errno)) {
            return false;
        }
        
        $errorType = self::getErrorType($errno);
        $message = "[$errorType] $errstr in $errfile on line $errline";
        error_log($message);
        
        if ($errno == E_USER_ERROR) {
            exit(1);
        }
        
        return true;
    }
    
    public static function handleException($exception) {
        $message = "Uncaught Exception: " . $exception->getMessage() . 
                  "\nStack trace: " . $exception->getTraceAsString() . 
                  "\nThrown in " . $exception->getFile() . " on line " . $exception->getLine();
        error_log($message);
        
        http_response_code(500);
        if (!headers_sent()) {
            header('Content-Type: text/html; charset=utf-8');
        }
        
        include __DIR__ . '/../views/errors/error.php';
        exit(1);
    }
    
    private static function getErrorType($errno) {
        switch ($errno) {
            case E_ERROR:
                return 'E_ERROR';
            case E_WARNING:
                return 'E_WARNING';
            case E_PARSE:
                return 'E_PARSE';
            case E_NOTICE:
                return 'E_NOTICE';
            case E_USER_ERROR:
                return 'E_USER_ERROR';
            case E_USER_WARNING:
                return 'E_USER_WARNING';
            case E_USER_NOTICE:
                return 'E_USER_NOTICE';
            default:
                return 'UNKNOWN';
        }
    }
}
