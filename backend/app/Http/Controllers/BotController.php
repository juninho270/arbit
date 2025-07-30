<?php

namespace App\Http\Controllers;

use App\Models\BotSettings;
use Illuminate\Http\Request;

class BotController extends Controller
{
    /**
     * Get user's bot settings.
     */
    public function getSettings(Request $request)
    {
        $settings = BotSettings::firstOrCreate(
            ['user_id' => $request->user()->id],
            [
                'is_active' => false,
                'min_profit' => 2.0,
                'max_amount' => 1000,
                'interval' => 300,
                'selected_coins' => ['bitcoin', 'ethereum'],
                'auto_reinvest' => true,
                'stop_loss' => 5.0,
            ]
        );

        return response()->json($settings);
    }

    /**
     * Update user's bot settings.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'is_active' => 'sometimes|boolean',
            'min_profit' => 'sometimes|numeric|min:0.1|max:10',
            'max_amount' => 'sometimes|numeric|min:10',
            'interval' => 'sometimes|integer|min:60',
            'selected_coins' => 'sometimes|array',
            'auto_reinvest' => 'sometimes|boolean',
            'stop_loss' => 'sometimes|numeric|min:1|max:20',
        ]);

        $settings = BotSettings::updateOrCreate(
            ['user_id' => $request->user()->id],
            $request->only([
                'is_active',
                'min_profit',
                'max_amount',
                'interval',
                'selected_coins',
                'auto_reinvest',
                'stop_loss',
            ])
        );

        return response()->json($settings);
    }

    /**
     * Get bot statistics.
     */
    public function getStatistics(Request $request)
    {
        // In a real implementation, you would calculate these from actual bot operations
        $statistics = [
            'total_operations' => 0,
            'total_profit' => 0,
            'success_rate' => 0,
            'average_profit' => 0,
            'uptime' => '0h 0m',
        ];

        return response()->json($statistics);
    }
}