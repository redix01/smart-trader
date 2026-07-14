<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('mining_plans')) {
            Schema::create('mining_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('min_amount', 20, 2);
            $table->decimal('max_amount', 20, 2)->nullable();
            $table->decimal('roi_percent', 5, 2);
            $table->integer('duration_days')->default(30);
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('mining_plans');
    }
};
