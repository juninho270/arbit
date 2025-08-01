<?php

namespace App\Core;
use App\Models\User;

class Router
{
    private $routes = [];
    private $middlewares = [];

    public function get($path, $handler, $middlewares = [])
    {
        $this->addRoute('GET', $path, $handler, $middlewares);
    }

    public function post($path, $handler, $middlewares = [])
    {
        $this->addRoute('POST', $path, $handler, $middlewares);
    }

    public function patch($path, $handler, $middlewares = [])
    {
        $this->addRoute('PATCH', $path, $handler, $middlewares);
    }

    public function delete($path, $handler, $middlewares = [])
    {
        $this->addRoute('DELETE', $path, $handler, $middlewares);
    }

    private function addRoute($method, $path, $handler, $middlewares = [])
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middlewares' => $middlewares
        ];
    }

    public function dispatch(Request $request, Response $response)
    {
        $requestMethod = $request->getMethod();
        $requestPath = $request->getPath();

        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && $this->matchPath($route['path'], $requestPath)) {
                // Extract parameters from path
                $params = $this->extractParams($route['path'], $requestPath);
                $request->setParams($params);

                // Check middlewares
                if (!$this->checkMiddlewares($route['middlewares'], $request, $response)) {
                    return;
                }

                // Handle the route
                if (is_string($route['handler'])) {
                    $this->handleStringRoute($route['handler'], $request, $response);
                } elseif (is_callable($route['handler'])) {
                    call_user_func($route['handler'], $request, $response);
                }
                return;
            }
        }

        // Route not found
        if (strpos($requestPath, '/api/') === 0) {
            $response->json(['error' => 'Route not found'], 404);
        } else {
            $response->send('404 - Page not found', 404);
        }
    }

    private function matchPath($routePath, $requestPath)
    {
        // Convert route path to regex pattern
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';
        
        return preg_match($pattern, $requestPath);
    }

    private function extractParams($routePath, $requestPath)
    {
        $params = [];
        
        // Extract parameter names from route path
        preg_match_all('/\{([^}]+)\}/', $routePath, $paramNames);
        
        // Extract parameter values from request path
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

    private function checkMiddlewares($middlewares, Request $request, Response $response)
    {
        foreach ($middlewares as $middleware) {
            if ($middleware === 'auth') {
                if (!Auth::check()) {
                    $response->json(['error' => 'Unauthorized'], 401);
                    return false;
                }
            } elseif ($middleware === 'admin') {
                if (!Auth::check() || !User::isAdmin(Auth::user())) {
                    $response->json(['error' => 'Forbidden'], 403);
                    return false;
                }
            }
        }
        return true;
    }

    private function handleStringRoute($handler, Request $request, Response $response)
    {
        list($controllerName, $method) = explode('@', $handler);
        
        // Add App\Controllers namespace if not present
        if (strpos($controllerName, '\\') === false) {
            $controllerName = 'App\\Controllers\\' . $controllerName;
        }

        if (!class_exists($controllerName)) {
            throw new \Exception("Controller {$controllerName} not found");
        }

        $controller = new $controllerName();
        
        if (!method_exists($controller, $method)) {
            throw new \Exception("Method {$method} not found in {$controllerName}");
        }

        $controller->$method($request, $response);
    }
}