<?php
class Router {
    private $routes = [];

    public function add($route, $callback) {
        $this->routes[$route] = $callback;
    }

    public function dispatch($route) {
        // Remove leading slash
        $route = ltrim($route, '/');
        
        // Check for exact match first
        if (isset($this->routes[$route])) {
            $this->executeRoute($this->routes[$route]);
            return;
        }

        // Check for pattern matches
        foreach ($this->routes as $pattern => $callback) {
            if ($this->matchRoute($pattern, $route)) {
                $this->executeRoute($callback);
                return;
            }
        }

        // No route found
        throw new Exception("Route not found: " . $route);
    }

    private function matchRoute($pattern, $route) {
        // Convert pattern to regex
        $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $pattern);
        $pattern = '/^' . str_replace('/', '\/', $pattern) . '$/';
        
        return preg_match($pattern, $route);
    }

    private function executeRoute($callback) {
        if (is_string($callback)) {
            $parts = explode('@', $callback);
            $controllerName = $parts[0];
            $methodName = $parts[1];

            $controllerFile = 'app/controllers/' . $controllerName . '.php';
            
            if (!file_exists($controllerFile)) {
                throw new Exception("Controller not found: " . $controllerName);
            }

            require_once $controllerFile;

            if (!class_exists($controllerName)) {
                throw new Exception("Controller class not found: " . $controllerName);
            }

            $controller = new $controllerName();

            if (!method_exists($controller, $methodName)) {
                throw new Exception("Method not found: " . $methodName . " in " . $controllerName);
            }

            $controller->$methodName();
        } elseif (is_callable($callback)) {
            call_user_func($callback);
        }
    }
}