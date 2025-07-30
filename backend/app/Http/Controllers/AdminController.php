<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ArbitrageOperation;
use App\Models\Investment;
use App\Models\SystemSettings;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Get admin dashboard statistics.
     */
    public function getStats(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'total_volume' => ArbitrageOperation::where('status', 'completed')->sum('amount'),
            'total_profit' => ArbitrageOperation::where('status', 'completed')->sum('profit'),
            'active_operations' => ArbitrageOperation::where('status', 'pending')->count(),
            'system_status' => 'online',
        ];

        return response()->json($stats);
    }

    /**
     * Get system settings.
     */
    public function getSystemSettings(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $settings = [
            'arbitrage_enabled' => SystemSettings::get('arbitrage_enabled', true),
            'bot_enabled' => SystemSettings::get('bot_enabled', true),
            'min_arbitrage_amount' => SystemSettings::get('min_arbitrage_amount', 100),
            'max_arbitrage_amount' => SystemSettings::get('max_arbitrage_amount', 10000),
            'arbitrage_fee' => SystemSettings::get('arbitrage_fee', 2.5),
            'bot_activation_fee' => SystemSettings::get('bot_activation_fee', 50),
            'maintenance_mode' => SystemSettings::get('maintenance_mode', false),
        ];

        return response()->json($settings);
    }

    /**
     * Update system settings.
     */
    public function updateSystemSettings(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $request->validate([
            'arbitrage_enabled' => 'sometimes|boolean',
            'bot_enabled' => 'sometimes|boolean',
            'min_arbitrage_amount' => 'sometimes|numeric|min:1',
            'max_arbitrage_amount' => 'sometimes|numeric|min:1',
            'arbitrage_fee' => 'sometimes|numeric|min:0|max:100',
            'bot_activation_fee' => 'sometimes|numeric|min:0',
            'maintenance_mode' => 'sometimes|boolean',
        ]);

        foreach ($request->only([
            'arbitrage_enabled',
            'bot_enabled',
            'min_arbitrage_amount',
            'max_arbitrage_amount',
            'arbitrage_fee',
            'bot_activation_fee',
            'maintenance_mode',
        ]) as $key => $value) {
            $type = is_bool($value) ? 'boolean' : (is_numeric($value) ? 'decimal' : 'string');
            SystemSettings::set($key, $value, $type);
        }

        return response()->json(['message' => 'Configurações atualizadas com sucesso']);
    }

    /**
     * Get recent activity.
     */
    public function getRecentActivity(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $activities = collect();

        // Recent users
        $recentUsers = User::orderBy('created_at', 'desc')->limit(5)->get();
        foreach ($recentUsers as $user) {
            $activities->push([
                'type' => 'user_registered',
                'message' => "Novo usuário registrado: {$user->name}",
                'created_at' => $user->created_at,
            ]);
        }

        // Recent operations
        $recentOperations = ArbitrageOperation::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        foreach ($recentOperations as $operation) {
            $activities->push([
                'type' => 'arbitrage_operation',
                'message' => "Operação de arbitragem: {$operation->cryptocurrency} por {$operation->user->name}",
                'created_at' => $operation->created_at,
            ]);
        }

        return response()->json($activities->sortByDesc('created_at')->values());
    }
}