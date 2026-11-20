<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Admin who placed the trade on the user's behalf (null when the user placed it). */
    public function up(): void
    {
        if (Schema::hasColumn('live_trades', 'placed_by')) {
            return;
        }
        Schema::table('live_trades', function (Blueprint $table) {
            $table->uuid('placed_by')->nullable()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('live_trades', function (Blueprint $table) {
            $table->dropColumn('placed_by');
        });
    }
};
