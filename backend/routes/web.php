<?php

/**
 * Web Routes
 * Define routes for web interface
 */

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

// Admin routes (protected by admin middleware)
$router->get('/admin', 'AdminController@index', ['auth', 'admin']);
$router->get('/admin/users', 'AdminController@users', ['auth', 'admin']);
$router->post('/admin/users/create', 'AdminController@createUser', ['auth', 'admin']);
$router->post('/admin/users/{id}/update', 'AdminController@updateUser', ['auth', 'admin']);
$router->post('/admin/users/{id}/delete', 'AdminController@deleteUser', ['auth', 'admin']);
$router->post('/admin/login-as/{id}', 'AdminController@loginAsUser', ['auth', 'admin']);

// Health check
$router->get('/health', function($request, $response) {
    $response->json([
        'status' => 'healthy',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
});