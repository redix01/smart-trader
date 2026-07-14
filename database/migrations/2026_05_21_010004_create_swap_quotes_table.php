<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('swap_quotes')) {
            Schema::create('swap_quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('from_currency', 10);
            $table->string('to_currency', 10);
            $table->decimal('from_amount', 20, 8);
            $table->decimal('to_amount', 20, 8);
            $table->decimal('rate', 20, 8);
            $table->decimal('fee', 20, 8)->default(0);
            $table->string('status', 32)->default('pending');
            $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('swap_quotes');
    }
};
