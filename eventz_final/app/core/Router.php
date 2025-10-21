<?php
/**
 * Router Class
 * Handles URL routing and dispatching to controllers
 */

class Router {
    private $routes = [];
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function get($uri, $controller) {
        $this->routes['GET'][$uri] = $controller;
    }
    
    public function post($uri, $controller) {
        $this->routes['POST'][$uri] = $controller;
    }
    
    public function dispatch() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];
        
        // Remove base path if it exists
        if (BASE_PATH !== '/' && strpos($uri, BASE_PATH) === 0) {
            $uri = substr($uri, strlen(BASE_PATH));
        }
        
        // Ensure URI starts with /
        if (empty($uri) || $uri === '') {
            $uri = '/';
        }
        
        // Debug logging
        error_log("Router: URI = '$uri', Method = '$method'");
        
        // Check if route exists
        if (isset($this->routes[$method][$uri])) {
            $handler = $this->routes[$method][$uri];
            
            if (is_callable($handler)) {
                call_user_func($handler);
            } else {
                list($controllerName, $action) = explode('@', $handler);
                
                $controllerFile = __DIR__ . '/../controllers/' . $controllerName . '.php';
                
                if (file_exists($controllerFile)) {
                    require_once $controllerFile;
                    
                    if (class_exists($controllerName)) {
                        $controller = new $controllerName();
                        
                        if (method_exists($controller, $action)) {
                            $controller->$action();
                        } else {
                            error_log("Method not found: $controllerName@$action");
                            $this->notFound();
                        }
                    } else {
                        error_log("Class not found: $controllerName");
                        $this->notFound();
                    }
                } else {
                    error_log("Controller file not found: $controllerFile");
                    $this->notFound();
                }
            }
        } else {
            error_log("Route not found: $method $uri");
            $this->notFound();
        }
    }
    
    private function notFound() {
        http_response_code(404);
        echo "404 - Page Not Found";
    }
}