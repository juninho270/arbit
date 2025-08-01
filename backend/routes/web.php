<?php

/**
 * Web Routes - Full PHP MVC
 * Todas as rotas da aplicação web
 */

// Rota raiz - redireciona baseado na autenticação
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

// Rotas de autenticação
$router->get('/login', 'WebAuthController@showLogin');
$router->post('/login', 'WebAuthController@login');
$router->get('/logout', 'WebAuthController@logout');

// Rotas do usuário (protegidas)
$router->get('/dashboard', 'DashboardController@index', ['auth']);
$router->get('/market', 'MarketController@index', ['auth']);
$router->get('/arbitrage', 'ArbitrageController@showManual', ['auth']);
$router->post('/arbitrage/execute', 'ArbitrageController@executeManual', ['auth']);
$router->get('/bot', 'BotController@index', ['auth']);
$router->post('/bot/settings', 'BotController@updateSettings', ['auth']);
$router->get('/investments', 'InvestmentController@index', ['auth']);
$router->post('/investments/create', 'InvestmentController@create', ['auth']);
$router->get('/settings', 'SettingsController@index', ['auth']);
$router->post('/settings/update', 'SettingsController@update', ['auth']);

// Rotas do admin (protegidas + admin)
$router->get('/admin', 'AdminController@index', ['auth', 'admin']);
$router->get('/admin/users', 'AdminController@users', ['auth', 'admin']);
$router->post('/admin/users/create', 'AdminController@createUser', ['auth', 'admin']);
$router->post('/admin/users/{id}/update', 'AdminController@updateUser', ['auth', 'admin']);
$router->post('/admin/users/{id}/delete', 'AdminController@deleteUser', ['auth', 'admin']);
$router->post('/admin/users/{id}/login-as', 'AdminController@loginAsUser', ['auth', 'admin']);
$router->get('/admin/operations', 'AdminController@operations', ['auth', 'admin']);
$router->get('/admin/cryptos', 'AdminController@cryptos', ['auth', 'admin']);
$router->post('/admin/cryptos/{id}/toggle', 'AdminController@toggleCrypto', ['auth', 'admin']);
$router->get('/admin/settings', 'AdminController@settings', ['auth', 'admin']);
$router->post('/admin/settings/update', 'AdminController@updateSettings', ['auth', 'admin']);

// Health check
$router->get('/health', function($request, $response) {
    $response->json([
        'status' => 'healthy',
        'timestamp' => date('Y-m-d H:i:s'),
        'version' => '2.0.0 - Full PHP MVC'
    ]);
});