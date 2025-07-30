<?php

namespace App\Http\Controllers;

use App\Models\ArbitrageOperation;
use App\Models\Cryptocurrency;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ArbitrageController extends Controller
{
    /**
     * Display a listing of arbitrage operations.
     */
    public function index(Request $request)
    {
        $query = ArbitrageOperation::with('user')
            ->orderBy('created_at', 'desc');

        // Non-admin users can only see their own operations
        if (!$request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        $operations = $query->paginate(20);

        return response()->json($operations);
    }

    /**
     * Execute a manual arbitrage operation.
     */
    public function executeManual(Request $request)
    {
        $request->validate([
            'cryptocurrency' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'coin_id' => 'required|string',
        ]);

        $user = $request->user();

        // Check if user has sufficient balance
        if ($user->balance < $request->amount) {
            return response()->json(['message' => 'Saldo insuficiente'], 400);
        }

        // Check if cryptocurrency is enabled for arbitrage
        $crypto = Cryptocurrency::where('coin_id', $request->coin_id)->first();
        if ($crypto && !$crypto->is_arbitrage_enabled) {
            return response()->json(['message' => 'Arbitragem desabilitada para esta criptomoeda'], 400);
        }

        // Get current price
        $price = $this->getCryptocurrencyPrice($request->coin_id);
        if ($price <= 0) {
            return response()->json(['message' => 'Não foi possível obter o preço atual'], 400);
        }

        // Simulate arbitrage profit (2% to 8%)
        $profitPercentage = rand(200, 800) / 100; // 2.00% to 8.00%
        $profit = ($request->amount * $profitPercentage) / 100;
        $executionTime = rand(3000, 5000); // 3-5 seconds

        // Create operation
        $operation = ArbitrageOperation::create([
            'user_id' => $user->id,
            'type' => 'manual',
            'cryptocurrency' => $request->cryptocurrency,
            'amount' => $request->amount,
            'buy_price' => $price,
            'sell_price' => $price * 1.05, // 5% higher
            'profit' => $profit,
            'profit_percentage' => $profitPercentage,
            'status' => 'pending',
            'execution_time' => $executionTime,
        ]);

        // Simulate execution delay
        sleep($executionTime / 1000);

        // Try to find real transaction hash
        $transactionResult = $this->findTransactionHash($request->coin_id, $request->amount);

        if (!$transactionResult) {
            // Cancel operation
            $operation->update([
                'status' => 'cancelled_no_hash',
                'no_hash_reason' => 'O sistema não encontrou transações possíveis para este token neste momento.',
                'completed_at' => now(),
            ]);

            // Auto-deactivate cryptocurrency
            Cryptocurrency::updateOrCreate(
                ['coin_id' => $request->coin_id],
                [
                    'is_arbitrage_enabled' => false,
                    'deactivation_reason' => "Auto-desativado: Falha em encontrar transações reais. Última tentativa: " . now()->format('d/m/Y H:i:s') . " - Valor: $" . number_format($request->amount, 2),
                ]
            );

            return response()->json($operation);
        }

        // Complete operation successfully
        $operation->update([
            'status' => 'completed',
            'transaction_hash' => $transactionResult['hash'],
            'chain' => $transactionResult['chain'],
            'completed_at' => now(),
        ]);

        // Update user balance
        $user->update(['balance' => $user->balance + $profit]);

        return response()->json($operation);
    }

    /**
     * Get user's recent operations.
     */
    public function recent(Request $request)
    {
        $operations = ArbitrageOperation::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json($operations);
    }

    /**
     * Get cryptocurrency price from CoinGecko.
     */
    private function getCryptocurrencyPrice(string $coinId): float
    {
        try {
            $response = Http::timeout(10)->get(env('COINGECKO_API_URL') . '/simple/price', [
                'ids' => $coinId,
                'vs_currencies' => 'usd'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data[$coinId]['usd'] ?? 0;
            }
        } catch (\Exception $e) {
            \Log::error('Error fetching price: ' . $e->getMessage());
        }

        return 0;
    }

    /**
     * Find transaction hash (simplified version).
     */
    private function findTransactionHash(string $coinId, float $amount): ?array
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