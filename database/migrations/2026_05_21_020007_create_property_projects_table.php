<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('property_projects')) {
            Schema::create('property_projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('region')->nullable();
            $table->text('description')->nullable();
            $table->string('strategy', 64)->nullable();
            $table->decimal('min_investment', 20, 2);
            $table->decimal('target_roi', 5, 2)->nullable();
            $table->string('status', 32)->default('open');
            $table->string('image')->nullable();
            $table->json('media')->nullable();
            $table->text('disclosure')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('property_projects');
    }
};
