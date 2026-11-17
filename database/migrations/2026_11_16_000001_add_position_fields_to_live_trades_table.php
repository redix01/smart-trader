<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Expand-only: adds nullable position fields so orders can be filled, held open and closed.
 * Safe for old code (it never reads these columns). Status values become:
 * pending (limit waiting) -> open (position) -> closed | liquidated; pending -> cancelled.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('live_trades', function (Blueprint $table) {
            $table->decimal('entry_price', 24, 8)->nullable()->after('price');
            $table->decimal('exit_price', 24, 8)->nullable()->after('entry_price');
            $table->decimal('take_profit', 24, 8)->nullable()->after('exit_price');
            $table->decimal('stop_loss', 24, 8)->nullable()->after('take_profit');
            $table->decimal('realized_pnl', 24, 8)->nullable()->after('stop_loss');
            $table->string('close_reason', 20)->nullable()->after('realized_pnl'); // manual, take_profit, stop_loss, liquidation
            $table->timestamp('closed_at')->nullable()->after('filled_at');
        });
    }

    public function down(): void
    {
        Schema::table('live_trades', function (Blueprint $table) {
            $table->dropColumn(['entry_price', 'exit_price', 'take_profit', 'stop_loss', 'realized_pnl', 'close_reason', 'closed_at']);
        });
    }
};
