<?php

namespace App\Http\Controllers;

use App\Models\Cryptocurrency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CryptocurrencyController extends Controller
{
    /**
     * Display a listing of cryptocurrencies.
     */
    public function index(Request $request)
    {
        $limit = $request->get('limit', 50);
        
        // Try to get from cache first
        $cacheKey = "cryptocurrencies_limit_{$limit}";
        $cryptocurrencies = Cache::get($cacheKey);

        if (!$cryptocurrencies) {
            // Fetch from CoinGecko API
            $cryptocurrencies = $this->fetchFromCoinGecko($limit);
            
            // Cache for 5 minutes
            Cache::put($cacheKey, $cryptocurrencies, 300);
        }

        return response()->json($cryptocurrencies);
    }

    /**
     * Get cryptocurrency price.
     */
    public function getPrice(Request $request, string $coinId)
    {
        $cacheKey = "crypto_price_{$coinId}";
        $price = Cache::get($cacheKey);

        if (!$price) {
            try {
                $response = Http::timeout(10)->get(env('COINGECKO_API_URL') . '/simple/price', [
                    'ids' => $coinId,
                    'vs_currencies' => 'usd'
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $price = $data[$coinId]['usd'] ?? 0;
                    
                    // Cache for 5 minutes
                    Cache::put($cacheKey, $price, 300);
                } else {
                    $price = 0;
                }
            } catch (\Exception $e) {
                $price = 0;
            }
        }

        return response()->json(['price' => $price]);
    }

    /**
     * Update cryptocurrency arbitrage status (Admin only).
     */
    public function updateArbitrageStatus(Request $request, string $coinId)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $request->validate([
            'enabled' => 'required|boolean',
            'reason' => 'nullable|string',
        ]);

        $crypto = Cryptocurrency::where('coin_id', $coinId)->first();

        if (!$crypto) {
            // Create new cryptocurrency record
            $crypto = Cryptocurrency::create([
                'coin_id' => $coinId,
                'symbol' => '',
                'name' => '',
                'is_arbitrage_enabled' => $request->enabled,
                'deactivation_reason' => $request->reason,
            ]);
        } else {
            $crypto->update([
                'is_arbitrage_enabled' => $request->enabled,
                'deactivation_reason' => $request->enabled ? null : $request->reason,
            ]);
        }

        return response()->json($crypto);
    }

    /**
     * Fetch cryptocurrencies from CoinGecko API.
     */
    private function fetchFromCoinGecko(int $limit)
    {
        try {
            $response = Http::timeout(10)->get(env('COINGECKO_API_URL') . '/coins/markets', [
                'vs_currency' => 'usd',
                'order' => 'market_cap_desc',
                'per_page' => $limit,
                'page' => 1,
                'sparkline' => true,
                'price_change_percentage' => '24h'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Merge with database settings
                return collect($data)->map(function ($crypto) {
                    $dbCrypto = Cryptocurrency::where('coin_id', $crypto['id'])->first();
                    
                    return array_merge($crypto, [
                        'isArbitrageEnabled' => $dbCrypto ? $dbCrypto->is_arbitrage_enabled : true,
                        'deactivationReason' => $dbCrypto ? $dbCrypto->deactivation_reason : null,
                    ]);
                })->toArray();
            }
        } catch (\Exception $e) {
            \Log::error('Error fetching cryptocurrencies: ' . $e->getMessage());
        }

        return [];
    }
}