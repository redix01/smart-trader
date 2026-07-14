<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 16)->default('market');
            $table->string('side', 8);
            $table->string('pair', 32);
            $table->decimal('price', 20, 8)->nullable();
            $table->decimal('amount', 20, 8);
            $table->decimal('filled', 20, 8)->default(0);
            $table->decimal('total', 20, 8);
            $table->decimal('fee', 20, 8)->default(0);
            $table->string('status', 32)->default('open');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['pair', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
