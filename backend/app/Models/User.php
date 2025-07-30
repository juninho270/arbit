<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'balance',
        'bot_balance',
        'role',
        'status',
        'last_login',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login' => 'datetime',
        'balance' => 'decimal:2',
        'bot_balance' => 'decimal:2',
        'password' => 'hashed',
    ];

    /**
     * Get the arbitrage operations for the user.
     */
    public function arbitrageOperations()
    {
        return $this->hasMany(ArbitrageOperation::class);
    }

    /**
     * Get the bot settings for the user.
     */
    public function botSettings()
    {
        return $this->hasOne(BotSettings::class);
    }

    /**
     * Get the investments for the user.
     */
    public function investments()
    {
        return $this->hasMany(Investment::class);
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}