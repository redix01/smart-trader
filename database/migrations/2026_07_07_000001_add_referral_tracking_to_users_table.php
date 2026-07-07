<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('referral_code')->nullable()->unique()->after('referral_balance');
            $table->foreignUuid('referred_by')->nullable()->after('referral_code')->constrained('users')->nullOnDelete();
        });

        // Generate unique referral codes for existing users.
        User::whereNull('referral_code')->chunk(100, function ($users) {
            foreach ($users as $user) {
                do {
                    $code = Str::upper(Str::random(8));
                } while (User::where('referral_code', $code)->exists());

                $user->referral_code = $code;
                $user->save();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referred_by']);
            $table->dropColumn(['referral_code', 'referred_by']);
        });
    }
};
