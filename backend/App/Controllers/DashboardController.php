<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\User;
use App\Models\ArbitrageOperation;

class DashboardController extends Controller
{
    public function index($request, $response)
    {
        // Verifica se o usuário está autenticado
        if (!Auth::check()) {
            $response->redirect('/login');
            return;
        }

        $user = Auth::user();

        // Buscar operações recentes
        $recentOperations = ArbitrageOperation::getRecentByUser($user['id'], 5);

        // Calcular estatísticas
        $totalOperations = ArbitrageOperation::countByUser($user['id']);
        $monthlyProfit = ArbitrageOperation::getMonthlyProfitByUser($user['id']);

        // Dados para a view
        $data = [
            'user' => $user,
            'recentOperations' => $recentOperations,
            'totalOperations' => $totalOperations,
            'monthlyProfit' => $monthlyProfit,
        ];

        // Renderiza a view com layout
        $this->renderLayout('dashboard', $data);
    }

    private function getExplorerUrl($hash, $chain = 'bsc')
    {
        $explorers = [
            'bsc' => 'https://bscscan.com/tx/',
            'eth' => 'https://etherscan.io/tx/',
            'polygon' => 'https://polygonscan.com/tx/',
            'avalanche' => 'https://snowtrace.io/tx/',
            'arbitrum' => 'https://arbiscan.io/tx/',
        ];
        
        $baseUrl = $explorers[$chain] ?? 'https://bscscan.com/tx/';
        return $baseUrl . $hash;
    }
}