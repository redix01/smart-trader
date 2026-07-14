<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('transactions')) {
            Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('transactionable');
            $table->string('type', 32);
            $table->string('currency', 10);
            $table->decimal('amount', 20, 8);
            $table->decimal('fee', 20, 8)->default(0);
            $table->decimal('balance_before', 20, 8)->default(0);
            $table->decimal('balance_after', 20, 8)->default(0);
            $table->string('status', 32)->default('pending');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'status']);
            $table->index('created_at');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
