<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Investment;
use App\Models\InvestmentPlan;
use App\Models\User;

/**
 * Investment Controller
 * Handles investment plans and user investments
 */
class InvestmentController extends Controller
{
    /**
     * Get investment plans
     */
    public function getPlans()
    {
        $plans = InvestmentPlan::where('is_active', 1);

        $this->response->json($plans);
    }

    /**
     * Get user's investments
     */
    public function getUserInvestments()
    {
        $this->requireAuth();
        
        $user = $this->user();
        
        $investments = Investment::query("
            SELECT i.*, ip.name as plan_name 
            FROM investments i 
            LEFT JOIN investment_plans ip ON i.investment_plan_id = ip.id 
            WHERE i.user_id = ? 
            ORDER BY i.created_at DESC
        ", [$user['id']]);

        $this->response->json($investments);
    }

    /**
     * Create a new investment
     */
    public function createInvestment()
    {
        $this->requireAuth();

        $data = $this->validate([
            'plan_id' => 'required|numeric',
            'amount' => 'required|numeric',
        ]);

        $user = $this->user();
        $plan = InvestmentPlan::find($data['plan_id']);

        if (!$plan) {
            $this->response->json([
                'error' => true,
                'message' => 'Plano de investimento não encontrado'
            ], 404);
        }

        // Validate amount range
        if ($data['amount'] < $plan['min_amount'] || $data['amount'] > $plan['max_amount']) {
            $this->response->json([
                'error' => true,
                'message' => "Valor deve estar entre {$plan['min_amount']} e {$plan['max_amount']}"
            ], 400);
        }

        // Check if user has sufficient balance
        if ($user['balance'] < $data['amount']) {
            $this->response->json([
                'error' => true,
                'message' => 'Saldo insuficiente'
            ], 400);
        }

        $expectedReturn = ($data['amount'] * $plan['total_return']) / 100;
        $endDate = date('Y-m-d H:i:s', strtotime('+' . $plan['duration'] . ' days'));

        $investment = Investment::create([
            'user_id' => $user['id'],
            'investment_plan_id' => $plan['id'],
            'amount' => $data['amount'],
            'expected_return' => $expectedReturn,
            'current_return' => 0,
            'duration' => $plan['duration'],
            'status' => 'active',
            'start_date' => date('Y-m-d H:i:s'),
            'end_date' => $endDate,
            'progress' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Deduct from user balance
        User::update($user['id'], ['balance' => $user['balance'] - $data['amount']]);

        // Add plan information to response
        $investment['plan_name'] = $plan['name'];

        $this->response->json($investment, 201);
    }

    /**
     * Get investment statistics
     */
    public function getStatistics()
    {
        $this->requireAuth();
        
        $user = $this->user();
        
        $investments = Investment::where('user_id', $user['id']);

        $totalInvested = 0;
        $totalEarned = 0;
        $activeCount = 0;

        foreach ($investments as $investment) {
            $totalInvested += $investment['amount'];
            $totalEarned += $investment['current_return'];
            if ($investment['status'] === 'active') {
                $activeCount++;
            }
        }

        $statistics = [
            'total_invested' => $totalInvested,
            'total_earned' => $totalEarned,
            'active_investments' => $activeCount,
            'roi_average' => $totalInvested > 0 ? ($totalEarned / $totalInvested) * 100 : 0,
        ];

        $this->response->json($statistics);
    }
}