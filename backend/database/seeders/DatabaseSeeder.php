<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\InvestmentPlan;
use App\Models\SystemSettings;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'balance' => 50000,
            'bot_balance' => 25000,
            'role' => 'admin',
            'status' => 'active',
        ]);

        // Create test user
        User::create([
            'name' => 'Usuário Teste',
            'email' => 'user@user.com',
            'password' => Hash::make('password'),
            'balance' => 10000,
            'bot_balance' => 5000,
            'role' => 'user',
            'status' => 'active',
        ]);

        // Create additional test users
        User::create([
            'name' => 'João Silva',
            'email' => 'joao@email.com',
            'password' => Hash::make('password'),
            'balance' => 5420.50,
            'bot_balance' => 2100.00,
            'role' => 'user',
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Maria Santos',
            'email' => 'maria@email.com',
            'password' => Hash::make('password'),
            'balance' => 8750.25,
            'bot_balance' => 0,
            'role' => 'user',
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Pedro Costa',
            'email' => 'pedro@email.com',
            'password' => Hash::make('password'),
            'balance' => 1250.00,
            'bot_balance' => 500.00,
            'role' => 'user',
            'status' => 'suspended',
        ]);

        // Create investment plans
        InvestmentPlan::create([
            'name' => 'Plano Iniciante',
            'description' => 'Ideal para iniciantes no mundo dos investimentos',
            'min_amount' => 100,
            'max_amount' => 1000,
            'daily_return' => 2.5,
            'duration' => 30,
            'total_return' => 75,
            'risk' => 'low',
            'is_active' => true,
        ]);

        InvestmentPlan::create([
            'name' => 'Plano Intermediário',
            'description' => 'Para investidores com experiência moderada',
            'min_amount' => 1000,
            'max_amount' => 5000,
            'daily_return' => 3.8,
            'duration' => 45,
            'total_return' => 171,
            'risk' => 'medium',
            'is_active' => true,
        ]);

        InvestmentPlan::create([
            'name' => 'Plano Avançado',
            'description' => 'Para investidores experientes',
            'min_amount' => 5000,
            'max_amount' => 20000,
            'daily_return' => 5.2,
            'duration' => 60,
            'total_return' => 312,
            'risk' => 'high',
            'is_active' => true,
        ]);

        // Create system settings
        SystemSettings::set('arbitrage_enabled', true, 'boolean', 'Permitir operações de arbitragem');
        SystemSettings::set('bot_enabled', true, 'boolean', 'Permitir ativação de bots');
        SystemSettings::set('min_arbitrage_amount', 100, 'decimal', 'Valor mínimo para arbitragem');
        SystemSettings::set('max_arbitrage_amount', 10000, 'decimal', 'Valor máximo para arbitragem');
        SystemSettings::set('arbitrage_fee', 2.5, 'decimal', 'Taxa de arbitragem em porcentagem');
        SystemSettings::set('bot_activation_fee', 50, 'decimal', 'Taxa de ativação do bot');
        SystemSettings::set('maintenance_mode', false, 'boolean', 'Modo de manutenção');
    }
}