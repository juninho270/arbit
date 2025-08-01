<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\BotSettings;

/**
 * Bot Controller
 * Handles bot settings and statistics
 */
class BotController extends Controller
{
    /**
     * Get user's bot settings
     */
    public function getSettings()
    {
        $this->requireAuth();
        
        $user = $this->user();
        
        $settings = BotSettings::whereFirst('user_id', $user['id']);
        
        if (!$settings) {
            // Create default settings
            $settings = BotSettings::create([
                'user_id' => $user['id'],
                'is_active' => 0,
                'min_profit' => 2.0,
                'max_amount' => 1000,
                'interval' => 300,
                'selected_coins' => json_encode(['bitcoin', 'ethereum']),
                'auto_reinvest' => 1,
                'stop_loss' => 5.0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        // Decode JSON fields
        if (isset($settings['selected_coins'])) {
            $settings['selected_coins'] = json_decode($settings['selected_coins'], true);
        }

        $this->response->json($settings);
    }

    /**
     * Update user's bot settings
     */
    public function updateSettings()
    {
        $this->requireAuth();
        
        $user = $this->user();
        
        $allowedFields = [
            'is_active', 'min_profit', 'max_amount', 'interval', 
            'selected_coins', 'auto_reinvest', 'stop_loss'
        ];
        
        $data = [];
        foreach ($allowedFields as $field) {
            if ($this->request->has($field)) {
                $value = $this->request->input($field);
                
                // Handle JSON fields
                if ($field === 'selected_coins' && is_array($value)) {
                    $value = json_encode($value);
                }
                
                // Handle boolean fields
                if (in_array($field, ['is_active', 'auto_reinvest'])) {
                    $value = $value ? 1 : 0;
                }
                
                $data[$field] = $value;
            }
        }

        if (empty($data)) {
            $this->response->json([
                'error' => true,
                'message' => 'Nenhum dado para atualizar'
            ], 400);
        }

        $data['updated_at'] = date('Y-m-d H:i:s');

        $settings = BotSettings::whereFirst('user_id', $user['id']);
        
        if ($settings) {
            $settings = BotSettings::update($settings['id'], $data);
        } else {
            $data['user_id'] = $user['id'];
            $data['created_at'] = date('Y-m-d H:i:s');
            $settings = BotSettings::create($data);
        }

        // Decode JSON fields for response
        if (isset($settings['selected_coins'])) {
            $settings['selected_coins'] = json_decode($settings['selected_coins'], true);
        }

        $this->response->json($settings);
    }

    /**
     * Get bot statistics
     */
    public function getStatistics()
    {
        $this->requireAuth();
        
        $user = $this->user();
        
        // In a real implementation, you would calculate these from actual bot operations
        $statistics = [
            'total_operations' => 0,
            'total_profit' => 0,
            'success_rate' => 0,
            'average_profit' => 0,
            'uptime' => '0h 0m',
        ];

        $this->response->json($statistics);
    }
}