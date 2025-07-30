<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArbitrageOperation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'cryptocurrency',
        'amount',
        'buy_price',
        'sell_price',
        'profit',
        'profit_percentage',
        'status',
        'transaction_hash',
        'chain',
        'no_hash_reason',
        'execution_time',
        'completed_at',
        'error_message',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'buy_price' => 'decimal:8',
        'sell_price' => 'decimal:8',
        'profit' => 'decimal:2',
        'profit_percentage' => 'decimal:4',
        'execution_time' => 'integer',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the operation.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the cryptocurrency for this operation.
     */
    public function cryptocurrency()
    {
        return $this->belongsTo(Cryptocurrency::class, 'cryptocurrency', 'name');
    }
}