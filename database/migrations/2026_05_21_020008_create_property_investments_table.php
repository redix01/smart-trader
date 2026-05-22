<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_investments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_project_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 20, 2);
            $table->decimal('expected_return', 20, 2)->default(0);
            $table->decimal('payout_received', 20, 2)->default(0);
            $table->string('status', 32)->default('active');
            $table->boolean('disclosure_signed')->default(false);
            $table->timestamp('signed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_investments');
    }
};
