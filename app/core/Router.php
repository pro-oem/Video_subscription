<?php
class Router {
    private $controller = 'HomeController';
    private $method = 'index';
    private $params = [];
    private $middleware;

    private $protectedRoutes = [
        'content' => ['authenticate', 'checkSubscription'],
        'admin' => ['authenticate', 'isAdmin'],
        'auth/process-payment' => ['authenticate', 'verifyCSRF'],
        'auth/subscribe' => ['authenticate']
    ];

    private $rateLimitedRoutes = [
        'content/chat' => true,
        'api/' => true
    ];

    public function __construct() {
        $this->middleware = Middleware::getInstance();
        
        // Parse URL and set controller/method
        $url = $this->parseUrl();
        
        // Generate CSRF token for forms
        $this->middleware->generateCSRFToken();
        
        // Set controller
        if (isset($url[0])) {
            $controllerName = ucfirst($url[0]) . 'Controller';
            if (file_exists('app/controllers/' . $controllerName . '.php')) {
                $this->controller = $controllerName;
                unset($url[0]);
            }
        }

        require_once 'app/controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;

        // Set method
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        // Set parameters
        $this->params = $url ? array_values($url) : [];
    }

    public function run() {
        try {
            // Apply route protection
            $this->applyRouteProtection();
            
            // Apply rate limiting if needed
            $this->applyRateLimiting();
            
            // Execute the controller method
            return call_user_func_array([$this->controller, $this->method], $this->params);
        } catch (Exception $e) {
            error_log($e->getMessage());
            
            // Handle errors appropriately based on request type
            if ($this->isApiRequest()) {
                $this->jsonResponse(['error' => 'An error occurred'], 500);
            } else {
                require_once 'app/views/errors/error.php';
            }
        }
    }

    private function parseUrl() {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }

    private function applyRouteProtection() {
        $currentRoute = strtolower(isset($_GET['url']) ? $_GET['url'] : '');
        
        // Check if route needs protection
        foreach ($this->protectedRoutes as $route => $middlewares) {
            if (strpos($currentRoute, $route) === 0) {
                foreach ($middlewares as $middleware) {
                    if (method_exists($this->middleware, $middleware)) {
                        $this->middleware->$middleware();
                    }
                }
            }
        }
    }

    private function applyRateLimiting() {
        $currentRoute = strtolower(isset($_GET['url']) ? $_GET['url'] : '');
        
        // Check if route needs rate limiting
        foreach ($this->rateLimitedRoutes as $route => $enabled) {
            if ($enabled && strpos($currentRoute, $route) === 0) {
                $this->middleware->checkRateLimit();
                break;
            }
        }
    }

    private function isApiRequest() {
        return (
            isset($_SERVER['HTTP_ACCEPT']) && 
            strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false
        ) || (
            isset($_SERVER['CONTENT_TYPE']) && 
            strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false
        );
    }

    private function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}