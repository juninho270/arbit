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
        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('investment_plan_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->decimal('expected_return', 15, 2);
            $table->decimal('current_return', 15, 2)->default(0);
            $table->integer('duration'); // days
            $table->enum('status', ['active', 'completed', 'cancelled']);
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->decimal('progress', 5, 2)->default(0); // percentage
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investments');
    }
};