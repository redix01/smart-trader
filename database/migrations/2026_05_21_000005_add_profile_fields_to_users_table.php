<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columns = [
            'avatar'       => ['type' => 'string', 'nullable' => true, 'after' => 'email'],
            'locale'       => ['type' => 'string', 'length' => 5, 'default' => 'en', 'after' => 'avatar'],
            'currency'     => ['type' => 'string', 'length' => 5, 'default' => 'USD', 'after' => 'locale'],
            'kyc_level'    => ['type' => 'string', 'length' => 32, 'default' => 'unverified', 'after' => 'currency'],
            'account_tier' => ['type' => 'string', 'length' => 32, 'default' => 'standard', 'after' => 'kyc_level'],
            'last_login_at' => ['type' => 'timestamp', 'nullable' => true, 'after' => 'account_tier'],
        ];

        foreach ($columns as $name => $def) {
            if (Schema::hasColumn('users', $name)) {
                continue;
            }
            Schema::table('users', function (Blueprint $table) use ($name, $def) {
                $col = match ($def['type']) {
                    'timestamp' => $table->timestamp($name)->nullable($def['nullable'] ?? false),
                    default    => $table->string($name, $def['length'] ?? 191)->nullable($def['nullable'] ?? false)->default($def['default'] ?? null),
                };
                if (isset($def['after'])) {
                    $col->after($def['after']);
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'locale', 'currency', 'kyc_level', 'account_tier', 'last_login_at']);
        });
    }
};
