<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Repair databases where the original referral migration was recorded but
     * its users-table changes are absent or only partially present.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'referral_code')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('referral_code')->nullable()->unique()->after('referral_balance');
            });
        }

        if (! Schema::hasColumn('users', 'referred_by')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignUuid('referred_by')
                    ->nullable()
                    ->after('referral_code')
                    ->constrained('users')
                    ->nullOnDelete();
            });
        }

        $usedCodes = DB::table('users')
            ->whereNotNull('referral_code')
            ->pluck('referral_code')
            ->flip()
            ->all();

        DB::table('users')
            ->whereNull('referral_code')
            ->select('id')
            ->chunkById(100, function ($users) use (&$usedCodes) {
                foreach ($users as $user) {
                    do {
                        $code = Str::upper(Str::random(8));
                    } while (isset($usedCodes[$code]));

                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['referral_code' => $code]);

                    $usedCodes[$code] = true;
                }
            });
    }

    /**
     * This repair is intentionally irreversible: the original migration owns
     * these columns, and rolling this migration back must not remove user data.
     */
    public function down(): void
    {
        // No-op.
    }
};
