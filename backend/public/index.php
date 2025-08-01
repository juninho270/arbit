<?php
 
/**
 * CryptoArb Pro - MVC Pure Application Entry Point
 * 
 * This file serves as the single entry point for all HTTP requests.
 * It loads configuration, handles routing, and dispatches requests to controllers.
 */

// Start session
session_start();

// Define application constants
define('APP_ROOT', dirname(__DIR__));
define('PUBLIC_PATH', __DIR__);
define('APP_PATH', APP_ROOT . '/App');
define('CONFIG_PATH', APP_ROOT . '/config');
define('VIEWS_PATH', APP_ROOT . '/views');
define('ROUTES_PATH', APP_ROOT . '/routes');

// Error reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Autoloader for our classes
spl_autoload_register(function ($class) {
    // Convert namespace to file path
    $file = APP_ROOT . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Load environment variables from .env file
require_once APP_PATH . '/Core/DotEnvLoader.php';
$envPath = APP_ROOT . '/.env';
if (file_exists($envPath)) {
    \App\Core\DotEnvLoader::load($envPath);
}

// Load configuration
require_once CONFIG_PATH . '/app.php';
require_once CONFIG_PATH . '/database.php';

// Load core classes
require_once APP_PATH . '/Core/Router.php';
require_once APP_PATH . '/Core/Controller.php';
require_once APP_PATH . '/Core/Model.php';
require_once APP_PATH . '/Core/Request.php';
require_once APP_PATH . '/Core/Response.php';
require_once APP_PATH . '/Core/Auth.php';

// Load models that are used in routes
require_once APP_PATH . '/Models/User.php';
require_once APP_PATH . '/Models/ArbitrageOperation.php';

// Initialize core components
$request = new App\Core\Request();
$response = new App\Core\Response();
$router = new App\Core\Router();

// Load routes
require_once ROUTES_PATH . '/web.php';
require_once ROUTES_PATH . '/api.php';

// Handle CORS for API requests
if (strpos($request->getPath(), '/api/') === 0) {
    $response->setCorsHeaders();
    
    // Handle preflight OPTIONS request
    if ($request->getMethod() === 'OPTIONS') {
        $response->send('', 200);
        exit;
    }
}

try {
    // Dispatch the request
    $router->dispatch($request, $response);
} catch (Exception $e) {
    // Handle errors
    if (strpos($request->getPath(), '/api/') === 0) {
        // API error response
        $response->json([
            'error' => true,
            'message' => $e->getMessage()
        ], 500);
    } else {
        // Web error response
        http_response_code(500);
        echo "<h1>Error 500</h1><p>" . htmlspecialchars($e->getMessage()) . "</p>";
    }
}