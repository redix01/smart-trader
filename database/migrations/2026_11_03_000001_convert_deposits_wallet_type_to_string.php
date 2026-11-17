<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The deposits.wallet_type column was historically a MySQL ENUM whose
     * allowed values changed across migrations (first without 'balance',
     * later with it). Any database missing the later migration rejects
     * deposits for the "Main Balance" wallet with a "Data truncated for
     * column 'wallet_type'" error, so the deposit is never created and
     * never appears in the user or admin transaction history.
     *
     * A plain string column accepts every validated wallet type and can
     * never drift out of sync with the application validation rules.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            // MODIFY preserves existing row values on MySQL.
            DB::statement("ALTER TABLE `deposits` MODIFY `wallet_type` VARCHAR(20) NULL");
            return;
        }

        Schema::table('deposits', function (Blueprint $table) {
            $table->dropColumn('wallet_type');
        });

        Schema::table('deposits', function (Blueprint $table) {
            $table->string('wallet_type', 20)->nullable()->after('payment_method_id');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `deposits` MODIFY `wallet_type` ENUM('balance', 'trading', 'holding', 'staking') NULL");
            return;
        }

        Schema::table('deposits', function (Blueprint $table) {
            $table->dropColumn('wallet_type');
        });

        Schema::table('deposits', function (Blueprint $table) {
            $table->enum('wallet_type', ['balance', 'trading', 'holding', 'staking'])->nullable()->after('payment_method_id');
        });
    }
};
