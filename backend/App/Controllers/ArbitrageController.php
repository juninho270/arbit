<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ArbitrageOperation;
use App\Models\Cryptocurrency;
use App\Models\User;

/**
 * Arbitrage Controller
 * Handles arbitrage operations
 */
class ArbitrageController extends Controller
{
    /**
     * Display a listing of arbitrage operations
     */
    public function index()
    {
        $this->requireAuth();
        
        $user = $this->user();
        
        if ($user['role'] === 'admin') {
            // Admin can see all operations
            $operations = ArbitrageOperation::query("
                SELECT ao.*, u.name as user_name 
                FROM arbitrage_operations ao 
                LEFT JOIN users u ON ao.user_id = u.id 
                ORDER BY ao.created_at DESC 
                LIMIT 20
            ");
        } else {
            // Users can only see their own operations
            $operations = ArbitrageOperation::query("
                SELECT * FROM arbitrage_operations 
                WHERE user_id = ? 
                ORDER BY created_at DESC 
                LIMIT 20
            ", [$user['id']]);
        }

        $this->response->json(['data' => $operations]);
    }

    /**
     * Execute a manual arbitrage operation
     */
    public function executeManual()
    {
        $this->requireAuth();

        $data = $this->validate([
            'cryptocurrency' => 'required',
            'amount' => 'required|numeric',
            'coin_id' => 'required',
        ]);

        $user = $this->user();

        // Check if user has sufficient balance
        if ($user['balance'] < $data['amount']) {
            $this->response->json([
                'error' => true,
                'message' => 'Saldo insuficiente'
            ], 400);
        }

        // Check if cryptocurrency is enabled for arbitrage
        $crypto = Cryptocurrency::whereFirst('coin_id', $data['coin_id']);
        if ($crypto && !$crypto['is_arbitrage_enabled']) {
            $this->response->json([
                'error' => true,
                'message' => 'Arbitragem desabilitada para esta criptomoeda'
            ], 400);
        }

        // Get current price
        $price = $this->getCryptocurrencyPrice($data['coin_id']);
        if ($price <= 0) {
            $this->response->json([
                'error' => true,
                'message' => 'Não foi possível obter o preço atual'
            ], 400);
        }

        // Simulate arbitrage profit (2% to 8%)
        $profitPercentage = rand(200, 800) / 100; // 2.00% to 8.00%
        $profit = ($data['amount'] * $profitPercentage) / 100;
        $executionTime = rand(3000, 5000); // 3-5 seconds

        // Create operation
        $operation = ArbitrageOperation::create([
            'user_id' => $user['id'],
            'type' => 'manual',
            'cryptocurrency' => $data['cryptocurrency'],
            'amount' => $data['amount'],
            'buy_price' => $price,
            'sell_price' => $price * 1.05, // 5% higher
            'profit' => $profit,
            'profit_percentage' => $profitPercentage,
            'status' => 'pending',
            'execution_time' => $executionTime,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Simulate execution delay
        sleep($executionTime / 1000);

        // Try to find real transaction hash
        $transactionResult = $this->findTransactionHash($data['coin_id'], $data['amount']);

        if (!$transactionResult) {
            // Cancel operation
            $operation = ArbitrageOperation::update($operation['id'], [
                'status' => 'cancelled_no_hash',
                'no_hash_reason' => 'O sistema não encontrou transações possíveis para este token neste momento.',
                'completed_at' => date('Y-m-d H:i:s'),
            ]);

            // Auto-deactivate cryptocurrency
            if ($crypto) {
                Cryptocurrency::update($crypto['id'], [
                    'is_arbitrage_enabled' => 0,
                    'deactivation_reason' => "Auto-desativado: Falha em encontrar transações reais. Última tentativa: " . date('d/m/Y H:i:s') . " - Valor: $" . number_format($data['amount'], 2),
                ]);
            } else {
                Cryptocurrency::create([
                    'coin_id' => $data['coin_id'],
                    'symbol' => '',
                    'name' => $data['cryptocurrency'],
                    'is_arbitrage_enabled' => 0,
                    'deactivation_reason' => "Auto-desativado: Falha em encontrar transações reais. Última tentativa: " . date('d/m/Y H:i:s') . " - Valor: $" . number_format($data['amount'], 2),
                ]);
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