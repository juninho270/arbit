<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use App\Models\InvestmentPlan;
use Illuminate\Http\Request;

class InvestmentController extends Controller
{
    /**
     * Get investment plans.
     */
    public function getPlans()
    {
        $plans = InvestmentPlan::where('is_active', true)->get();

        return response()->json($plans);
    }

    /**
     * Get user's investments.
     */
    public function getUserInvestments(Request $request)
    {
        $investments = Investment::with('investmentPlan')
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($investments);
    }

    /**
     * Create a new investment.
     */
    public function createInvestment(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:investment_plans,id',
            'amount' => 'required|numeric|min:1',
        ]);

        $user = $request->user();
        $plan = InvestmentPlan::findOrFail($request->plan_id);

        // Validate amount range
        if ($request->amount < $plan->min_amount || $request->amount > $plan->max_amount) {
            return response()->json([
                'message' => "Valor deve estar entre {$plan->min_amount} e {$plan->max_amount}"
            ], 400);
        }

        // Check if user has sufficient balance
        if ($user->balance < $request->amount) {
            return response()->json(['message' => 'Saldo insuficiente'], 400);
        }

        $expectedReturn = ($request->amount * $plan->total_return) / 100;
        $endDate = now()->addDays($plan->duration);

        $investment = Investment::create([
            'user_id' => $user->id,
            'investment_plan_id' => $plan->id,
            'amount' => $request->amount,
            'expected_return' => $expectedReturn,
            'current_return' => 0,
            'duration' => $plan->duration,
            'status' => 'active',
            'start_date' => now(),
            'end_date' => $endDate,
            'progress' => 0,
        ]);

        // Deduct from user balance
        $user->update(['balance' => $user->balance - $request->amount]);

        return response()->json($investment->load('investmentPlan'), 201);
    }

    /**
     * Get investment statistics.
     */
    public function getStatistics(Request $request)
    {
        $investments = Investment::where('user_id', $request->user()->id)->get();

        $statistics = [
            'total_invested' => $investments->sum('amount'),
            'total_earned' => $investments->sum('current_return'),
            'active_investments' => $investments->where('status', 'active')->count(),
            'roi_average' => $investments->sum('amount') > 0 
                ? ($investments->sum('current_return') / $investments->sum('amount')) * 100 
                : 0,
        ];

        return response()->json($statistics);
    }
}