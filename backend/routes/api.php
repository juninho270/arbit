<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CryptocurrencyController;
use App\Http\Controllers\ArbitrageController;
use App\Http\Controllers\BotController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/login-as-user', [AuthController::class, 'loginAsUser']);

    // User routes
    Route::apiResource('users', UserController::class);
    Route::patch('/users/{user}/balance', [UserController::class, 'updateBalance']);

    // Cryptocurrency routes
    Route::get('/cryptocurrencies', [CryptocurrencyController::class, 'index']);
    Route::get('/cryptocurrencies/{coinId}/price', [CryptocurrencyController::class, 'getPrice']);
    Route::patch('/cryptocurrencies/{coinId}/arbitrage-status', [CryptocurrencyController::class, 'updateArbitrageStatus']);

    // Arbitrage routes
    Route::get('/arbitrage/operations', [ArbitrageController::class, 'index']);
    Route::post('/arbitrage/execute-manual', [ArbitrageController::class, 'executeManual']);
    Route::get('/arbitrage/recent', [ArbitrageController::class, 'recent']);

    // Bot routes
    Route::get('/bot/settings', [BotController::class, 'getSettings']);
    Route::patch('/bot/settings', [BotController::class, 'updateSettings']);
    Route::get('/bot/statistics', [BotController::class, 'getStatistics']);

    // Investment routes
    Route::get('/investments/plans', [InvestmentController::class, 'getPlans']);
    Route::get('/investments/user', [InvestmentController::class, 'getUserInvestments']);
    Route::post('/investments', [InvestmentController::class, 'createInvestment']);
    Route::get('/investments/statistics', [InvestmentController::class, 'getStatistics']);

    // Admin routes
    Route::middleware('admin')->group(function () {
        Route::get('/admin/stats', [AdminController::class, 'getStats']);
        Route::get('/admin/system-settings', [AdminController::class, 'getSystemSettings']);
        Route::patch('/admin/system-settings', [AdminController::class, 'updateSystemSettings']);
        Route::get('/admin/recent-activity', [AdminController::class, 'getRecentActivity']);
    });
});