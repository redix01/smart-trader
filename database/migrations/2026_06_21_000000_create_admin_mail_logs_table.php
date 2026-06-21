<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_mail_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('admin_user_id')->nullable();
            $table->string('sender_email');
            $table->string('sender_name')->nullable();
            $table->string('recipient_source');
            $table->json('recipients');
            $table->unsignedInteger('recipient_count')->default(0);
            $table->string('subject');
            $table->text('message');
            $table->string('header_color', 7)->default('#3B82F6');
            $table->string('accent_label')->nullable();
            $table->string('footer_note')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->foreign('admin_user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->index('sender_email');
            $table->index('recipient_source');
            $table->index('sent_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_mail_logs');
    }
};
