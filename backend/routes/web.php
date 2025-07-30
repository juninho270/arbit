<?php

/**
 * Web Routes
 * Define routes for web interface
 */

// Home page
$router->get('/', function($request, $response) {
    $response->json([
        'message' => 'CryptoArb Pro API',
        'version' => '1.0.0',
        'status' => 'online'
    ]);
});

// Health check
$router->get('/health', function($request, $response) {
    $response->json([
        'status' => 'healthy',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
});