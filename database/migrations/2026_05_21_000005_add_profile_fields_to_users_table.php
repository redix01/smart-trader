<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('email');
            $table->string('locale', 5)->default('en')->after('avatar');
            $table->string('currency', 5)->default('USD')->after('locale');
            $table->string('kyc_level', 32)->default('unverified')->after('currency');
            $table->string('account_tier', 32)->default('standard')->after('kyc_level');
            $table->timestamp('last_login_at')->nullable()->after('account_tier');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'locale', 'currency', 'kyc_level', 'account_tier', 'last_login_at']);
        });
    }
};
