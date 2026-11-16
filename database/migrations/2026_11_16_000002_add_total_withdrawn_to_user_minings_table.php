<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Tracks how much of total_mined has already been paid out, so a mining withdraw never pays twice. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_minings', function (Blueprint $table) {
            $table->decimal('total_withdrawn', 20, 8)->default(0)->after('total_mined');
        });
    }

    public function down(): void
    {
        Schema::table('user_minings', function (Blueprint $table) {
            $table->dropColumn('total_withdrawn');
        });
    }
};
