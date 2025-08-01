<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestmentPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'min_amount',
        'max_amount',
        'daily_return',
        'duration',
        'total_return',
        'risk',
        'is_active',
    ];

    protected $casts = [
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'daily_return' => 'decimal:4',
        'duration' => 'integer',
        'total_return' => 'decimal:4',
        'is_active' => 'boolean',
    ];

    /**
     * Get the investments for this plan.
     */
    public function investments()
    {
        return $this->hasMany(Investment::class);
    }
}