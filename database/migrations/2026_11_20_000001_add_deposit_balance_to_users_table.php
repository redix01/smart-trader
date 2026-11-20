<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Deposits wallet: funds an admin credits straight to the user from their profile. */
    public function up(): void
    {
        if (Schema::hasColumn('users', 'deposit_balance')) {
            return;
        }
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('deposit_balance', 15, 2)->default(0.00)->after('balance');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('deposit_balance');
        });
    }
};
