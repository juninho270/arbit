<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\User;
use App\Models\ArbitrageOperation;

class AdminController extends Controller
{
    public function index($request, $response)
    {
        if (!Auth::check() || !User::isAdmin(Auth::user())) {
            $response->redirect('/login');
            return;
        }

        $user = Auth::user();
        
        // Estatísticas do sistema
        $stats = [
            'total_users' => count(User::all()),
            'active_users' => count(User::where('status', 'active')),
            'total_operations' => ArbitrageOperation::count(),
            'total_volume' => ArbitrageOperation::getTotalVolume(),
            'total_profit' => ArbitrageOperation::getTotalProfit(),
            'system_status' => 'online'
        ];

        // Atividade recente
        $recentActivity = [
            [
                'type' => 'user_registered',
                'message' => 'Novo usuário registrado: João Silva',
                'time' => '2 min atrás'
            ],
            [
                'type' => 'arbitrage_operation',
                'message' => 'Operação de arbitragem: Bitcoin por Maria Santos',
                'time' => '5 min atrás'
            ],
            [
                'type' => 'bot_activated',
                'message' => 'Bot ativado por Pedro Costa',
                'time' => '8 min atrás'
            ]
        ];

        $data = [
            'user' => $user,
            'stats' => $stats,
            'recentActivity' => $recentActivity
        ];

        $this->renderLayout('admin/dashboard', $data);
    }

    public function users($request, $response)
    {
        if (!Auth::check() || !User::isAdmin(Auth::user())) {
            $response->redirect('/login');
            return;
        }

        $user = Auth::user();
        $users = User::all();

        $data = [
            'user' => $user,
            'users' => $users
        ];

        $this->renderLayout('admin/users', $data);
    }

    public function createUser($request, $response)
    {
        if (!Auth::check() || !User::isAdmin(Auth::user())) {
            $response->redirect('/login');
            return;
        }

        try {
            $name = $request->getPost('name');
            $email = $request->getPost('email');
            $balance = (float)$request->getPost('balance');
            $botBalance = (float)$request->getPost('bot_balance');
            $role = $request->getPost('role');
            $status = $request->getPost('status');

            if (!$name || !$email) {
                throw new \Exception('Nome e email são obrigatórios');
            }

            $userData = [
                'name' => $this->sanitize($name),
                'email' => $this->sanitize($email),
                'password' => 'password', // Senha padrão
                'balance' => $balance,
                'bot_balance' => $botBalance,
                'role' => $role ?: 'user',
                'status' => $status ?: 'active'
            ];

            User::create($userData);
            $response->redirect('/admin/users?success=user_created');
        } catch (\Exception $e) {
            $response->redirect('/admin/users?error=' . urlencode($e->getMessage()));
        }
    }

    public function updateUser($request, $response)
    {
        if (!Auth::check() || !User::isAdmin(Auth::user())) {
            $response->redirect('/login');
            return;
        }

        try {
            $id = $request->getParam('id');
            $name = $request->getPost('name');
            $email = $request->getPost('email');
            $balance = (float)$request->getPost('balance');
            $botBalance = (float)$request->getPost('bot_balance');
            $role = $request->getPost('role');
            $status = $request->getPost('status');

            $updateData = [
                'name' => $this->sanitize($name),
                'email' => $this->sanitize($email),
                'balance' => $balance,
                'bot_balance' => $botBalance,
                'role' => $role,
                'status' => $status
            ];

            User::update($id, $updateData);
            $response->redirect('/admin/users?success=user_updated');
        } catch (\Exception $e) {
            $response->redirect('/admin/users?error=' . urlencode($e->getMessage()));
        }
    }

    public function deleteUser($request, $response)
    {
        if (!Auth::check() || !User::isAdmin(Auth::user())) {
            $response->redirect('/login');
            return;
        }

        try {
            $id = $request->getParam('id');
            User::delete($id);
            $response->redirect('/admin/users?success=user_deleted');
        } catch (\Exception $e) {
            $response->redirect('/admin/users?error=' . urlencode($e->getMessage()));
        }
    }

    public function loginAsUser($request, $response)
    {
        if (!Auth::check() || !User::isAdmin(Auth::user())) {
            $response->redirect('/login');
            return;
        }

        try {
            $id = $request->getParam('id');
            $targetUser = User::find($id);
            
            if (!$targetUser) {
                throw new \Exception('Usuário não encontrado');
            }

            // Fazer login como o usuário
            $_SESSION['user_id'] = $targetUser['id'];
            $_SESSION['impersonated_by'] = Auth::id();
            
            $response->redirect('/dashboard');
        } catch (\Exception $e) {
            $response->redirect('/admin/users?error=' . urlencode($e->getMessage()));
        }
    }

    public function operations($request, $response)
    {
        if (!Auth::check() || !User::isAdmin(Auth::user())) {
            $response->redirect('/login');
            return;
        }

        $user = Auth::user();
        $operations = ArbitrageOperation::getAllWithUsers();

        $data = [
            'user' => $user,
            'operations' => $operations
        ];

        $this->renderLayout('admin/operations', $data);
    }

    public function cryptos($request, $response)
    {
        if (!Auth::check() || !User::isAdmin(Auth::user())) {
            $response->redirect('/login');
            return;
        }

        $user = Auth::user();
        
        // Buscar criptomoedas (simulado)
        $cryptocurrencies = $this->getCryptocurrencies();

        $data = [
            'user' => $user,
            'cryptocurrencies' => $cryptocurrencies
        ];

        $this->renderLayout('admin/cryptos', $data);
    }

    public function settings($request, $response)
    {
        if (!Auth::check() || !User::isAdmin(Auth::user())) {
            $response->redirect('/login');
            return;
        }

        $user = Auth::user();
        
        $settings = [
            'arbitrage_enabled' => true,
            'bot_enabled' => true,
            'min_arbitrage_amount' => 100,
            'max_arbitrage_amount' => 10000,
            'arbitrage_fee' => 2.5,
            'bot_activation_fee' => 50,
            'maintenance_mode' => false
        ];

        $data = [
            'user' => $user,
            'settings' => $settings
        ];

        $this->renderLayout('admin/settings', $data);
    }

    private function getCryptocurrencies()
    {
        return [
            [
                'id' => 'bitcoin',
                'symbol' => 'btc',
                'name' => 'Bitcoin',
                'current_price' => 43250.75,
                'price_change_percentage_24h' => 2.45,
                'market_cap' => 847392847392,
                'volume_24h' => 28473928473,
                'image' => 'https://assets.coingecko.com/coins/images/1/large/bitcoin.png',
                'is_arbitrage_enabled' => true
            ],
            [
                'id' => 'ethereum',
                'symbol' => 'eth',
                'name' => 'Ethereum',
                'current_price' => 2650.30,
                'price_change_percentage_24h' => -1.23,
                'market_cap' => 318473928473,
                'volume_24h' => 15847392847,
                'image' => 'https://assets.coingecko.com/coins/images/279/large/ethereum.png',
                'is_arbitrage_enabled' => true
            ],
            [
                'id' => 'binancecoin',
                'symbol' => 'bnb',
                'name' => 'BNB',
                'current_price' => 315.45,
                'price_change_percentage_24h' => 0.87,
                'market_cap' => 48473928473,
                'volume_24h' => 1847392847,
                'image' => 'https://assets.coingecko.com/coins/images/825/large/bnb-icon2_2x.png',
                'is_arbitrage_enabled' => false
            ]
        ];
    }
}