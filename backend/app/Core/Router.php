<?php

namespace App\Core;

/**
 * Simple Router Class
 * Handles URL routing and dispatches requests to controllers
 */
class Router
{
    private $routes = [];
    private $middlewares = [];

    /**
     * Add a GET route
     */
    public function get($path, $handler, $middleware = [])
    {
        $this->addRoute('GET', $path, $handler, $middleware);
    }

    /**
     * Add a POST route
     */
    public function post($path, $handler, $middleware = [])
    {
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    /**
     * Add a PUT route
     */
    public function put($path, $handler, $middleware = [])
    {
        $this->addRoute('PUT', $path, $handler, $middleware);
    }

    /**
     * Add a PATCH route
     */
    public function patch($path, $handler, $middleware = [])
    {
        $this->addRoute('PATCH', $path, $handler, $middleware);
    }

    /**
     * Add a DELETE route
     */
    public function delete($path, $handler, $middleware = [])
    {
        $this->addRoute('DELETE', $path, $handler, $middleware);
    }

    /**
     * Add a route with any method
     */
    private function addRoute($method, $path, $handler, $middleware = [])
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }

    /**
     * Dispatch the request to the appropriate controller
     */
    public function dispatch(Request $request, Response $response)
    {
        $method = $request->getMethod();
        $path = $request->getPath();

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPath($route['path'], $path)) {
                // Extract parameters from URL
                $params = $this->extractParams($route['path'], $path);
                
                // Run middleware
                foreach ($route['middleware'] as $middleware) {
                    $middlewareClass = "App\\Middleware\\{$middleware}";
                    if (class_exists($middlewareClass)) {
                        $middlewareInstance = new $middlewareClass();
                        if (!$middlewareInstance->handle($request, $response)) {
                            return; // Middleware blocked the request
                        }
                    }
                }

                // Call the handler
                if (is_callable($route['handler'])) {
                    // Closure handler
                    call_user_func_array($route['handler'], [$request, $response] + $params);
                } elseif (is_string($route['handler'])) {
                    // Controller@method handler
                    $this->callController($route['handler'], $request, $response, $params);
                }
                return;
            }
        }

        // No route found
        if (strpos($path, '/api/') === 0) {
            $response->json(['error' => 'Route not found'], 404);
        } else {
            http_response_code(404);
            echo "<h1>404 - Page Not Found</h1>";
        }
    }

    /**
     * Check if the route path matches the request path
     */
    private function matchPath($routePath, $requestPath)
    {
        // Convert route parameters to regex
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';
        
        return preg_match($pattern, $requestPath);
    }

    /**
     * Extract parameters from URL
     */
    private function extractParams($routePath, $requestPath)
    {
        $params = [];
        
        // Find parameter names in route
        preg_match_all('/\{([^}]+)\}/', $routePath, $paramNames);
        
        // Extract values from request path
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';
        
        if (preg_match($pattern, $requestPath, $matches)) {
            array_shift($matches); // Remove full match
            
            foreach ($paramNames[1] as $index => $paramName) {
                if (isset($matches[$index])) {
                    $params[$paramName] = $matches[$index];
                }
            }
        }
        
        return $params;
    }

    /**
     * Call a controller method
     */
    private function callController($handler, Request $request, Response $response, $params = [])
    {
        list($controllerName, $method) = explode('@', $handler);
        
        $controllerClass = "App\\Controllers\\{$controllerName}";
        
        if (!class_exists($controllerClass)) {
            throw new \Exception("Controller {$controllerClass} not found");
        }
        
        $controller = new $controllerClass($request, $response);
        
        if (!method_exists($controller, $method)) {
            throw new \Exception("Method {$method} not found in {$controllerClass}");
        }
        
        // Call the method with parameters
        call_user_func_array([$controller, $method], $params);
    }
}