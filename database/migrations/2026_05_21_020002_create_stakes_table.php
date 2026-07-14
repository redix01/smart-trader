<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('stakes')) {
            Schema::create('stakes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staking_plan_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 20, 8);
            $table->decimal('accrued_rewards', 20, 8)->default(0);
            $table->string('status', 32)->default('active');
            $table->timestamp('start_date');
            $table->timestamp('end_date')->nullable();
            $table->timestamp('last_payout_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('stakes');
    }
};
