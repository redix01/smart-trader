<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staking_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('currency', 10);
            $table->string('icon')->nullable();
            $table->decimal('min_amount', 20, 8);
            $table->decimal('max_amount', 20, 8)->nullable();
            $table->decimal('apy', 5, 2);
            $table->string('payout_cycle', 32)->default('weekly');
            $table->integer('duration_days')->default(30);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staking_plans');
    }
};
