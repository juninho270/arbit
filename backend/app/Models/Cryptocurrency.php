<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cryptocurrency extends Model
{
    use HasFactory;

    protected $fillable = [
        'coin_id',
        'symbol',
        'name',
        'current_price',
        'price_change_percentage_24h',
        'market_cap',
        'volume_24h',
        'image',
        'contract_address',
        'is_arbitrage_enabled',
        'deactivation_reason',
        'last_updated',
    ];

    protected $casts = [
        'current_price' => 'decimal:8',
        'price_change_percentage_24h' => 'decimal:4',
        'market_cap' => 'integer',
        'volume_24h' => 'integer',
        'is_arbitrage_enabled' => 'boolean',
        'last_updated' => 'datetime',
    ];

    /**
     * Get the arbitrage operations for this cryptocurrency.
     */
    public function arbitrageOperations()
    {
        return $this->hasMany(ArbitrageOperation::class, 'cryptocurrency', 'name');
    }
}