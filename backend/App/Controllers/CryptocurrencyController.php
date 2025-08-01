<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cryptocurrency;

/**
 * Cryptocurrency Controller
 * Handles cryptocurrency data and arbitrage settings
 */
class CryptocurrencyController extends Controller
{
    /**
     * Display a listing of cryptocurrencies
     */
    public function index()
    {
        $limit = $this->request->query('limit', 50);
        
        // Try to get from cache first (simple file cache)
        $cacheFile = APP_ROOT . '/storage/cache/cryptocurrencies_' . $limit . '.json';
        $cacheTime = 300; // 5 minutes
        
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
            $cryptocurrencies = json_decode(file_get_contents($cacheFile), true);
        } else {
            // Fetch from CoinGecko API
            $cryptocurrencies = $this->fetchFromCoinGecko($limit);
            
            // Create cache directory if it doesn't exist
            $cacheDir = dirname($cacheFile);
            if (!is_dir($cacheDir)) {
                mkdir($cacheDir, 0755, true);
            }
            
            // Cache the result
            file_put_contents($cacheFile, json_encode($cryptocurrencies));
        }

        $this->response->json($cryptocurrencies);
    }

    /**
     * Get cryptocurrency price
     */
    public function getPrice($coinId)
    {
        $cacheFile = APP_ROOT . '/storage/cache/crypto_price_' . $coinId . '.json';
        $cacheTime = 300; // 5 minutes
        
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
            $price = json_decode(file_get_contents($cacheFile), true);
        } else {
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
                    $price = $data[$coinId]['usd'] ?? 0;
                } else {
                    $price = 0;
                }
                
                // Create cache directory if it doesn't exist
                $cacheDir = dirname($cacheFile);
                if (!is_dir($cacheDir)) {
                    mkdir($cacheDir, 0755, true);
                }
                
                // Cache the result
                file_put_contents($cacheFile, json_encode($price));
                
            } catch (Exception $e) {
                $price = 0;
            }
        }

        $this->response->json(['price' => $price]);
    }

    /**
     * Update cryptocurrency arbitrage status (Admin only)
     */
    public function updateArbitrageStatus($coinId)
    {
        $this->requireAdmin();

        $data = $this->validate([
            'enabled' => 'required',
            'reason' => '',
        ]);

        $crypto = Cryptocurrency::whereFirst('coin_id', $coinId);

        if (!$crypto) {
            // Create new cryptocurrency record
            $crypto = Cryptocurrency::create([
                'coin_id' => $coinId,
                'symbol' => '',
                'name' => '',
                'is_arbitrage_enabled' => $data['enabled'] ? 1 : 0,
                'deactivation_reason' => $data['reason'] ?? null,
            ]);
        } else {
            $updateData = [
                'is_arbitrage_enabled' => $data['enabled'] ? 1 : 0,
                'deactivation_reason' => $data['enabled'] ? null : ($data['reason'] ?? null),
            ];
            
            $crypto = Cryptocurrency::update($crypto['id'], $updateData);
        }

        $this->response->json($crypto);
    }

    /**
     * Fetch cryptocurrencies from CoinGecko API
     */
    private function fetchFromCoinGecko($limit)
    {
        try {
            $config = include CONFIG_PATH . '/app.php';
            $url = $config['coingecko_api_url'] . '/coins/markets?' . http_build_query([
                'vs_currency' => 'usd',
                'order' => 'market_cap_desc',
                'per_page' => $limit,
                'page' => 1,
                'sparkline' => 'true',
                'price_change_percentage' => '24h'
            ]);

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
                
                // Merge with database settings
                $result = [];
                foreach ($data as $crypto) {
                    $dbCrypto = Cryptocurrency::whereFirst('coin_id', $crypto['id']);
                    
                    $crypto['isArbitrageEnabled'] = $dbCrypto ? (bool)$dbCrypto['is_arbitrage_enabled'] : true;
                    $crypto['deactivationReason'] = $dbCrypto ? $dbCrypto['deactivation_reason'] : null;
                    
                    $result[] = $crypto;
                }
                
                return $result;
            }
        } catch (Exception $e) {
            error_log('Error fetching cryptocurrencies: ' . $e->getMessage());
        }

        return [];
    }
}