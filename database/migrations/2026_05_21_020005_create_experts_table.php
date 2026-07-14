<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('experts')) {
            Schema::create('experts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('avatar')->nullable();
            $table->decimal('win_rate', 5, 2)->default(0);
            $table->decimal('profit_share', 5, 2)->default(0);
            $table->string('status', 32)->default('verified');
            $table->decimal('total_volume', 20, 2)->default(0);
            $table->text('bio')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('experts');
    }
};
