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
            $response->redirect('/admin');
        } else {
            $response->redirect('/dashboard');
        }
    } else {
        $response->redirect('/login');
    }
});

// Authentication routes
$router->get('/login', 'WebAuthController@showLogin');
$router->post('/login', 'WebAuthController@login');
$router->get('/logout', 'WebAuthController@logout');
$router->post('/logout', 'WebAuthController@logout');

// Dashboard routes (protected)
$router->get('/dashboard', 'DashboardController@index', ['auth']);

// Market routes
$router->get('/market', 'MarketController@index', ['auth']);

// Arbitrage routes
$router->get('/arbitrage', 'ArbitrageController@showManual', ['auth']);
$router->post('/arbitrage/execute', 'ArbitrageController@executeManual', ['auth']);

// Bot routes
$router->get('/bot', 'BotController@index', ['auth']);
$router->post('/bot/settings', 'BotController@updateSettings', ['auth']);

// Investment routes
$router->get('/investments', 'InvestmentController@index', ['auth']);
$router->post('/investments/create', 'InvestmentController@create', ['auth']);

// Settings routes
$router->get('/settings', 'SettingsController@index', ['auth']);
$router->post('/settings/update', 'SettingsController@update', ['auth']);

// Admin routes removidas - o frontend React gerencia as rotas /admin
// As APIs de admin estão disponíveis em /api/admin/*

// Health check
$router->get('/health', function($request, $response) {
    $response->json([
        'status' => 'healthy',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
});