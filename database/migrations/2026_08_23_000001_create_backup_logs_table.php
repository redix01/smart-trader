<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backup_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('admin_id');
            $table->enum('type', ['csv', 'sql']);
            $table->string('table_name')->nullable();
            $table->unsignedInteger('row_count')->default(0);
            $table->unsignedBigInteger('file_size')->default(0);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('admin_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_logs');
    }
};
