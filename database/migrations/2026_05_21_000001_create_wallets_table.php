<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('wallets')) {
            Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('currency', 10);
            $table->string('label')->nullable();
            $table->decimal('balance', 20, 8)->default(0);
            $table->decimal('locked_balance', 20, 8)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'currency']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
