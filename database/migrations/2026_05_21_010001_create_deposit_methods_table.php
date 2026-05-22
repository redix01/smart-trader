<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deposit_methods', function (Blueprint $table) {
            $table->id();
            $table->string('currency', 10);
            $table->string('network', 32);
            $table->string('label');
            $table->string('wallet_address');
            $table->string('icon')->nullable();
            $table->decimal('min_amount', 20, 2)->default(0);
            $table->decimal('max_amount', 20, 2)->nullable();
            $table->decimal('fee_fixed', 20, 8)->default(0);
            $table->decimal('fee_percent', 5, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deposit_methods');
    }
};
