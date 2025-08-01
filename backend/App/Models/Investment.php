<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'investment_plan_id',
        'amount',
        'expected_return',
        'current_return',
        'duration',
        'status',
        'start_date',
        'end_date',
        'progress',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expected_return' => 'decimal:2',
        'current_return' => 'decimal:2',
        'duration' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'progress' => 'decimal:2',
    ];

    /**
     * Get the user that owns the investment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the investment plan for this investment.
     */
    public function investmentPlan()
    {
        return $this->belongsTo(InvestmentPlan::class);
    }
}