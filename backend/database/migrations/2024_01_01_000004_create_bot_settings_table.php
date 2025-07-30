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
        Schema::create('bot_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('is_active')->default(false);
            $table->decimal('min_profit', 8, 4)->default(2.0);
            $table->decimal('max_amount', 15, 2)->default(1000);
            $table->integer('interval')->default(300); // seconds
            $table->json('selected_coins')->nullable();
            $table->boolean('auto_reinvest')->default(true);
            $table->decimal('stop_loss', 8, 4)->default(5.0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bot_settings');
    }
};