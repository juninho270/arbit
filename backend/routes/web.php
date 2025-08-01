<?php

/**
 * Web Routes
 * Define routes for web interface
 */

// Import required classes
use App\Core\Auth;
use App\Models\User;

// Home page - redireciona para dashboard se logado, senão para login
$router->get('/', function($request, $response) {
    if (Auth::check()) {
        $user = Auth::user();
        if (User::isAdmin($user)) {
            // Para admin, servir o React app que gerencia /admin
            $response->redirect('/#/admin');
        } else {
            // Para usuário, servir o React app que gerencia /dashboard  
            $response->redirect('/#/');
        }
    } else {
        // Para não logado, servir o React app que gerencia login
        $response->redirect('/#/login');
    }
});

// Serve React app for all non-API routes
$router->get('/admin', function($request, $response) {
    $response->redirect('/#/admin');
});

$router->get('/dashboard', function($request, $response) {
    $response->redirect('/#/');
});

$router->get('/market', function($request, $response) {
    $response->redirect('/#/market');
});

$router->get('/arbitrage', function($request, $response) {
    $response->redirect('/#/arbitrage');
});

$router->get('/bot', function($request, $response) {
    $response->redirect('/#/bot');
});

$router->get('/investments', function($request, $response) {
    $response->redirect('/#/investments');
});

$router->get('/settings', function($request, $response) {
    $response->redirect('/#/settings');
});

// Catch-all route for React Router (SPA)
$router->get('/{path}', function($request, $response) {
    // Serve the React app index.html for any non-API route
    $indexPath = PUBLIC_PATH . '/index.html';
    if (file_exists($indexPath)) {
        $response->send(file_get_contents($indexPath));
    } else {
        $response->send('React app not found', 404);
    }
});

// Admin routes removidas - o frontend React gerencia as rotas /admin
// As APIs de admin estão disponíveis em /api/admin/*

// Health check
$router->get('/health', function($request, $response) {
    $response->json([
        'status' => 'healthy',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
});