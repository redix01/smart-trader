<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('deposits')) {
            Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('deposit_method_id')->constrained();
            $table->decimal('amount', 20, 2);
            $table->decimal('fee', 20, 8)->default(0);
            $table->decimal('net_amount', 20, 2);
            $table->string('currency', 10);
            $table->string('status', 32)->default('pending');
            $table->string('proof_path')->nullable();
            $table->string('proof_mime')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->index(['user_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};
