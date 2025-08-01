<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;

class MarketController extends Controller
{
    public function index($request, $response)
    {
        if (!Auth::check()) {
            $response->redirect('/login');
            return;
        }

        $user = Auth::user();
        
        // Buscar criptomoedas (simulado - em produção viria da API)
        $cryptocurrencies = $this->getCryptocurrencies();

        $data = [
            'user' => $user,
            'cryptocurrencies' => $cryptocurrencies,
        ];

        $this->renderLayout('market', $data);
    }

    private function getCryptocurrencies()
    {
        // Dados simulados das principais criptomoedas
        return [
            [
                'id' => 'bitcoin',
                'symbol' => 'btc',
                'name' => 'Bitcoin',
                'current_price' => 43250.75,
                'price_change_percentage_24h' => 2.45,
                'market_cap' => 847392847392,
                'volume_24h' => 28473928473,
                'image' => 'https://assets.coingecko.com/coins/images/1/large/bitcoin.png'
            ],
            [
                'id' => 'ethereum',
                'symbol' => 'eth',
                'name' => 'Ethereum',
                'current_price' => 2650.30,
                'price_change_percentage_24h' => -1.23,
                'market_cap' => 318473928473,
                'volume_24h' => 15847392847,
                'image' => 'https://assets.coingecko.com/coins/images/279/large/ethereum.png'
            ],
            [
                'id' => 'binancecoin',
                'symbol' => 'bnb',
                'name' => 'BNB',
                'current_price' => 315.45,
                'price_change_percentage_24h' => 0.87,
                'market_cap' => 48473928473,
                'volume_24h' => 1847392847,
                'image' => 'https://assets.coingecko.com/coins/images/825/large/bnb-icon2_2x.png'
            ],
            [
                'id' => 'cardano',
                'symbol' => 'ada',
                'name' => 'Cardano',
                'current_price' => 0.485,
                'price_change_percentage_24h' => 3.21,
                'market_cap' => 17384729384,
                'volume_24h' => 847392847,
                'image' => 'https://assets.coingecko.com/coins/images/975/large/cardano.png'
            ],
            [
                'id' => 'solana',
                'symbol' => 'sol',
                'name' => 'Solana',
                'current_price' => 98.75,
                'price_change_percentage_24h' => -2.15,
                'market_cap' => 42847392847,
                'volume_24h' => 2847392847,
                'image' => 'https://assets.coingecko.com/coins/images/4128/large/solana.png'
            ],
            [
                'id' => 'ripple',
                'symbol' => 'xrp',
                'name' => 'XRP',
                'current_price' => 0.625,
                'price_change_percentage_24h' => 1.45,
                'market_cap' => 33847392847,
                'volume_24h' => 1847392847,
                'image' => 'https://assets.coingecko.com/coins/images/44/large/xrp-symbol-white-128.png'
            ],
            [
                'id' => 'polkadot',
                'symbol' => 'dot',
                'name' => 'Polkadot',
                'current_price' => 7.25,
                'price_change_percentage_24h' => -0.85,
                'market_cap' => 9847392847,
                'volume_24h' => 584739284,
                'image' => 'https://assets.coingecko.com/coins/images/12171/large/polkadot.png'
            ],
            [
                'id' => 'dogecoin',
                'symbol' => 'doge',
                'name' => 'Dogecoin',
                'current_price' => 0.085,
                'price_change_percentage_24h' => 4.32,
                'market_cap' => 12847392847,
                'volume_24h' => 847392847,
                'image' => 'https://assets.coingecko.com/coins/images/5/large/dogecoin.png'
            ],
            [
                'id' => 'avalanche-2',
                'symbol' => 'avax',
                'name' => 'Avalanche',
                'current_price' => 36.85,
                'price_change_percentage_24h' => 2.75,
                'market_cap' => 14847392847,
                'volume_24h' => 684739284,
                'image' => 'https://assets.coingecko.com/coins/images/12559/large/Avalanche_Circle_RedWhite_Trans.png'
            ],
            [
                'id' => 'chainlink',
                'symbol' => 'link',
                'name' => 'Chainlink',
                'current_price' => 14.75,
                'price_change_percentage_24h' => -1.65,
                'market_cap' => 8847392847,
                'volume_24h' => 484739284,
                'image' => 'https://assets.coingecko.com/coins/images/877/large/chainlink-new-logo.png'
            ]
        ];
    }
}