<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\ArbitrageOperation;
use App\Models\Investment;
use App\Models\SystemSettings;

/**
 * Admin Controller
 * Handles admin dashboard and system management
 */
class AdminController extends Controller
{
    /**
     * Get admin dashboard statistics
     */
    public function getStats()
    {
        $this->requireAdmin();

        $totalUsers = count(User::all());
        $activeUsers = count(User::where('status', 'active'));
        
        $completedOperations = ArbitrageOperation::where('status', 'completed');
        $totalVolume = 0;
        $totalProfit = 0;
        foreach ($completedOperations as $op) {
            $totalVolume += $op['amount'];
            $totalProfit += $op['profit'];
        }
        
        $activeOperations = count(ArbitrageOperation::where('status', 'pending'));

        $stats = [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'total_volume' => $totalVolume,
            'total_profit' => $totalProfit,
            'active_operations' => $activeOperations,
            'system_status' => 'online',
        ];

        $this->response->json($stats);
    }

    /**
     * Get system settings
     */
    public function getSystemSettings()
    {
        $this->requireAdmin();

        $settings = [
            'arbitrage_enabled' => SystemSettings::get('arbitrage_enabled', true),
            'bot_enabled' => SystemSettings::get('bot_enabled', true),
            'min_arbitrage_amount' => SystemSettings::get('min_arbitrage_amount', 100),
            'max_arbitrage_amount' => SystemSettings::get('max_arbitrage_amount', 10000),
            'arbitrage_fee' => SystemSettings::get('arbitrage_fee', 2.5),
            'bot_activation_fee' => SystemSettings::get('bot_activation_fee', 50),
            'maintenance_mode' => SystemSettings::get('maintenance_mode', false),
        ];

        $this->response->json($settings);
    }

    /**
     * Update system settings
     */
    public function updateSystemSettings()
    {
        $this->requireAdmin();

        $allowedSettings = [
            'arbitrage_enabled',
            'bot_enabled',
            'min_arbitrage_amount',
            'max_arbitrage_amount',
            'arbitrage_fee',
            'bot_activation_fee',
            'maintenance_mode',
        ];

        $updated = false;
        foreach ($allowedSettings as $setting) {
            if ($this->request->has($setting)) {
                $value = $this->request->input($setting);
                $type = is_bool($value) ? 'boolean' : (is_numeric($value) ? 'decimal' : 'string');
                SystemSettings::set($setting, $value, $type);
                $updated = true;
            }
        }

        if (!$updated) {
            $this->response->json([
                'error' => true,
                'message' => 'Nenhuma configuração para atualizar'
            ], 400);
        }

        $this->response->json([
            'message' => 'Configurações atualizadas com sucesso'
        ]);
    }

    /**
     * Get recent activity
     */
    public function getRecentActivity()
    {
        $this->requireAdmin();

        $activities = [];

        // Recent users
        $recentUsers = User::query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");
        foreach ($recentUsers as $user) {
            $activities[] = [
                'type' => 'user_registered',
                'message' => "Novo usuário registrado: {$user['name']}",
                'created_at' => $user['created_at'],
            ];
        }

        // Recent operations
        $recentOperations = ArbitrageOperation::query("
            SELECT ao.*, u.name as user_name 
            FROM arbitrage_operations ao 
            LEFT JOIN users u ON ao.user_id = u.id 
            ORDER BY ao.created_at DESC 
            LIMIT 5
        ");
        
        foreach ($recentOperations as $operation) {
            $activities[] = [
                'type' => 'arbitrage_operation',
                'message' => "Operação de arbitragem: {$operation['cryptocurrency']} por {$operation['user_name']}",
                'created_at' => $operation['created_at'],
            ];
        }

        // Sort by created_at desc
        usort($activities, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        $this->response->json(array_values($activities));
    }
}