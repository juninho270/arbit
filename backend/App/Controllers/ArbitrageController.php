<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\ArbitrageOperation;
use App\Models\Cryptocurrency;
use App\Models\User;

class ArbitrageController extends Controller
{
    public function showManual($request, $response)
    {
        if (!Auth::check()) {
            $response->redirect('/login');
            return;
        }

        $user = Auth::user();
        
        // Buscar criptomoedas disponíveis
        $cryptocurrencies = $this->getCryptocurrencies();
        
        // Operações recentes do usuário
        $recentOperations = ArbitrageOperation::getRecentByUser($user['id'], 5);

        $data = [
            'user' => $user,
            'cryptocurrencies' => $cryptocurrencies,
            'recentOperations' => $recentOperations
        ];

        $this->renderLayout('arbitrage/manual', $data);
    }

    public function executeManual($request, $response)
    {
        if (!Auth::check()) {
            $response->redirect('/login');
            return;
        }

        try {
            $cryptocurrency = $request->getPost('cryptocurrency');
            $amount = (float)$request->getPost('amount');
            $coinId = $request->getPost('coin_id');

            if (!$cryptocurrency || !$amount || !$coinId) {
                throw new \Exception('Dados incompletos');
            }

            $user = Auth::user();

            // Verificar saldo
            if ($user['balance'] < $amount) {
                throw new \Exception('Saldo insuficiente');
            }

            // Simular operação de arbitragem
            $price = $this->getCryptocurrencyPrice($coinId);
            $profitPercentage = rand(200, 800) / 100; // 2% a 8%
            $profit = ($amount * $profitPercentage) / 100;
            $executionTime = rand(3000, 5000);

            // Criar operação
            $operationData = [
                'user_id' => $user['id'],
                'type' => 'manual',
                'cryptocurrency' => $cryptocurrency,
                'amount' => $amount,
                'buy_price' => $price,
                'sell_price' => $price * 1.05,
                'profit' => $profit,
                'profit_percentage' => $profitPercentage,
                'status' => 'completed',
                'transaction_hash' => '0x' . bin2hex(random_bytes(32)),
                'chain' => 'bsc',
                'execution_time' => $executionTime
            ];

            ArbitrageOperation::create($operationData);

            // Atualizar saldo do usuário
            User::update($user['id'], ['balance' => $user['balance'] + $profit]);

            $response->redirect('/arbitrage?success=operation_completed&profit=' . $profit);
        } catch (\Exception $e) {
            $response->redirect('/arbitrage?error=' . urlencode($e->getMessage()));
        }
    }

    private function getCryptocurrencies()
    {
        return [
            [
                'id' => 'bitcoin',
                'symbol' => 'btc',
                'name' => 'Bitcoin',
                'current_price' => 43250.75,
                'price_change_percentage_24h' => 2.45
            ],
            [
                'id' => 'ethereum',
                'symbol' => 'eth',
                'name' => 'Ethereum',
                'current_price' => 2650.30,
                'price_change_percentage_24h' => -1.23
            ],
            [
                'id' => 'binancecoin',
                'symbol' => 'bnb',
                'name' => 'BNB',
                'current_price' => 315.45,
                'price_change_percentage_24h' => 0.87
            ]
        ];
    }

    private function getCryptocurrencyPrice($coinId)
    {
        // Simular preços
        $prices = [
            'bitcoin' => 43250.75,
            'ethereum' => 2650.30,
            'binancecoin' => 315.45
        ];
        
        return $prices[$coinId] ?? 1000;
    }
}
            }

            $this->response->json($operation);
            return;
        }

        // Complete operation successfully
        $operation = ArbitrageOperation::update($operation['id'], [
            'status' => 'completed',
            'transaction_hash' => $transactionResult['hash'],
            'chain' => $transactionResult['chain'],
            'completed_at' => date('Y-m-d H:i:s'),
        ]);

        // Update user balance
        User::update($user['id'], ['balance' => $user['balance'] + $profit]);

        $this->response->json($operation);
    }

    /**
     * Get user's recent operations
     */
    public function recent()
    {
        $this->requireAuth();
        
        $user = $this->user();
        
        $operations = ArbitrageOperation::query("
            SELECT * FROM arbitrage_operations 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT 10
        ", [$user['id']]);

        $this->response->json($operations);
    }

    /**
     * Get cryptocurrency price from CoinGecko
     */
    private function getCryptocurrencyPrice($coinId)
    {
        try {
            $config = include CONFIG_PATH . '/app.php';
            $url = $config['coingecko_api_url'] . '/simple/price?ids=' . $coinId . '&vs_currencies=usd';
            
            $context = stream_context_create([
                'http' => [
                    'timeout' => 10,
                    'method' => 'GET',
                    'header' => 'User-Agent: CryptoArb Pro/1.0'
                ]
            ]);
            
            $response = file_get_contents($url, false, $context);
            
            if ($response !== false) {
                $data = json_decode($response, true);
                return $data[$coinId]['usd'] ?? 0;
            }
        } catch (Exception $e) {
            error_log('Error fetching price: ' . $e->getMessage());
        }

        return 0;
    }

    /**
     * Find transaction hash (simplified version)
     */
    private function findTransactionHash($coinId, $amount)
    {
        // This is a simplified version - in production you would implement
        // the full Moralis API integration as in the original code
        
        // For now, simulate finding a transaction 70% of the time
        if (rand(1, 100) <= 70) {
            return [
                'hash' => '0x' . bin2hex(random_bytes(32)),
                'chain' => 'bsc'
            ];
        }

        return null;
    }
}