<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BotSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'is_active',
        'min_profit',
        'max_amount',
        'interval',
        'selected_coins',
        'auto_reinvest',
        'stop_loss',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'min_profit' => 'decimal:4',
        'max_amount' => 'decimal:2',
        'interval' => 'integer',
        'selected_coins' => 'array',
        'auto_reinvest' => 'boolean',
        'stop_loss' => 'decimal:4',
    ];

    /**
     * Get the user that owns the bot settings.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}