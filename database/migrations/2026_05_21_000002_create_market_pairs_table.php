<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_pairs', function (Blueprint $table) {
            $table->id();
            $table->string('base_currency', 10);
            $table->string('quote_currency', 10);
            $table->decimal('current_price', 20, 8)->default(0);
            $table->decimal('price_change_24h', 20, 8)->default(0);
            $table->decimal('volume_24h', 20, 2)->default(0);
            $table->decimal('high_24h', 20, 8)->default(0);
            $table->decimal('low_24h', 20, 8)->default(0);
            $table->decimal('market_cap', 20, 2)->default(0);
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['base_currency', 'quote_currency']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_pairs');
    }
};
