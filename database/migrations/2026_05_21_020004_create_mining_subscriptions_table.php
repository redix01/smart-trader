<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mining_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mining_plan_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 20, 2);
            $table->decimal('earned_so_far', 20, 2)->default(0);
            $table->string('status', 32)->default('active');
            $table->timestamp('start_date');
            $table->timestamp('end_date')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mining_subscriptions');
    }
};
