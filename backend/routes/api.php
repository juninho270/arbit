<?php

/**
 * API Routes
 * Define routes for API endpoints
 */

// Authentication routes
$router->post('/api/login', 'AuthController@login');
$router->post('/api/register', 'AuthController@register');
$router->post('/api/logout', 'AuthController@logout', ['auth']);
$router->get('/api/me', 'AuthController@me', ['auth']);
$router->post('/api/login-as-user', 'AuthController@loginAsUser', ['auth', 'admin']);

// User routes
$router->get('/api/users', 'UserController@index', ['auth', 'admin']);
$router->post('/api/users', 'UserController@store', ['auth', 'admin']);
$router->get('/api/users/{id}', 'UserController@show', ['auth']);
$router->patch('/api/users/{id}', 'UserController@update', ['auth']);
$router->delete('/api/users/{id}', 'UserController@destroy', ['auth', 'admin']);
$router->patch('/api/users/{id}/balance', 'UserController@updateBalance', ['auth']);

// Cryptocurrency routes
$router->get('/api/cryptocurrencies', 'CryptocurrencyController@index');
$router->get('/api/cryptocurrencies/{coinId}/price', 'CryptocurrencyController@getPrice');
$router->patch('/api/cryptocurrencies/{coinId}/arbitrage-status', 'CryptocurrencyController@updateArbitrageStatus', ['auth', 'admin']);

// Arbitrage routes
$router->get('/api/arbitrage/operations', 'ArbitrageController@index', ['auth']);
$router->post('/api/arbitrage/execute-manual', 'ArbitrageController@executeManual', ['auth']);
$router->get('/api/arbitrage/recent', 'ArbitrageController@recent', ['auth']);

// Bot routes
$router->get('/api/bot/settings', 'BotController@getSettings', ['auth']);
$router->patch('/api/bot/settings', 'BotController@updateSettings', ['auth']);
$router->get('/api/bot/statistics', 'BotController@getStatistics', ['auth']);

// Investment routes
$router->get('/api/investments/plans', 'InvestmentController@getPlans');
$router->get('/api/investments/user', 'InvestmentController@getUserInvestments', ['auth']);
$router->post('/api/investments', 'InvestmentController@createInvestment', ['auth']);
$router->get('/api/investments/statistics', 'InvestmentController@getStatistics', ['auth']);

// Admin routes
$router->get('/api/admin/stats', 'AdminController@getStats', ['auth', 'admin']);
$router->get('/api/admin/system-settings', 'AdminController@getSystemSettings', ['auth', 'admin']);
$router->patch('/api/admin/system-settings', 'AdminController@updateSystemSettings', ['auth', 'admin']);
$router->get('/api/admin/recent-activity', 'AdminController@getRecentActivity', ['auth', 'admin']);