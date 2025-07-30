<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cryptocurrencies', function (Blueprint $table) {
            $table->id();
            $table->string('coin_id')->unique(); // CoinGecko ID
            $table->string('symbol');
            $table->string('name');
            $table->decimal('current_price', 20, 8)->nullable();
            $table->decimal('price_change_percentage_24h', 10, 4)->nullable();
            $table->bigInteger('market_cap')->nullable();
            $table->bigInteger('volume_24h')->nullable();
            $table->string('image')->nullable();
            $table->string('contract_address')->nullable();
            $table->boolean('is_arbitrage_enabled')->default(true);
            $table->text('deactivation_reason')->nullable();
            $table->timestamp('last_updated')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cryptocurrencies');
    }
};