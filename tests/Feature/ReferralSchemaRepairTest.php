<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class ReferralSchemaRepairTest extends TestCase
{
    private string $originalConnection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalConnection = Config::get('database.default');

        Config::set('database.connections.referral_schema_test', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
        Config::set('database.default', 'referral_schema_test');
        DB::purge('referral_schema_test');

        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->decimal('referral_balance', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        DB::purge('referral_schema_test');
        Config::set('database.default', $this->originalConnection);

        parent::tearDown();
    }

    public function test_user_creation_survives_until_referral_columns_are_migrated(): void
    {
        $user = User::create([
            'name' => 'Schema Drift User',
            'email' => 'schema-drift@example.com',
            'password' => 'password',
        ]);

        $this->assertDatabaseHas('users', ['id' => $user->id]);
        $this->assertNull($user->referral_code);
    }

    public function test_repair_migration_restores_columns_and_backfills_every_user(): void
    {
        $users = collect(range(1, 205))->map(fn (int $number) => [
            'id' => (string) Str::uuid(),
            'name' => "User {$number}",
            'email' => "user{$number}@example.com",
            'password' => 'password',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $users->chunk(100)->each(fn ($chunk) => DB::table('users')->insert($chunk->all()));

        $migration = require database_path('migrations/2026_07_16_000001_repair_referral_tracking_columns.php');
        $migration->up();

        $this->assertTrue(Schema::hasColumns('users', ['referral_code', 'referred_by']));
        $this->assertSame(0, DB::table('users')->whereNull('referral_code')->count());
        $this->assertSame(205, DB::table('users')->distinct()->count('referral_code'));
    }
}
