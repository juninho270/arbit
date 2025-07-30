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
        Schema::create('arbitrage_operations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['manual', 'bot']);
            $table->string('cryptocurrency');
            $table->decimal('amount', 15, 2);
            $table->decimal('buy_price', 20, 8);
            $table->decimal('sell_price', 20, 8);
            $table->decimal('profit', 15, 2);
            $table->decimal('profit_percentage', 8, 4);
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled_no_hash']);
            $table->string('transaction_hash')->nullable();
            $table->string('chain')->nullable();
            $table->text('no_hash_reason')->nullable();
            $table->integer('execution_time'); // in milliseconds
            $table->timestamp('completed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arbitrage_operations');
    }
};