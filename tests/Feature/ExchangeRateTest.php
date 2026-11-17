<?php

namespace Tests\Feature;

use App\Models\ExchangeRate;
use App\Models\User;
use App\Services\ExchangeRateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ExchangeRateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_seeded_rates_make_conversion_work_before_any_refresh(): void
    {
        $fx = app(ExchangeRateService::class);

        $this->assertEqualsWithDelta(75.605, $fx->convert(100, 'USD', 'GBP'), 0.001);
        $this->assertSame(100.0, $fx->convert(100, 'USD', 'USD'));
    }

    public function test_cross_rates_go_through_usd(): void
    {
        $fx = app(ExchangeRateService::class);

        // 100 GBP -> USD -> EUR : 100 / 0.75605 * 0.90481
        $this->assertEqualsWithDelta(100 / 0.75605 * 0.90481, $fx->convert(100, 'GBP', 'EUR'), 0.001);
    }

    public function test_refresh_stores_new_rates_and_clears_the_cache(): void
    {
        $fx = app(ExchangeRateService::class);
        $fx->convert(1, 'USD', 'GBP'); // warm the cache
        Http::fake(['api.frankfurter.dev/*' => Http::response(['base' => 'USD', 'rates' => ['GBP' => 0.8, 'EUR' => 0.95, 'XXX' => 3]])]);

        $this->assertSame(2, $fx->refresh());           // XXX is not a supported currency
        $this->assertEqualsWithDelta(80.0, $fx->convert(100, 'USD', 'GBP'), 0.0001);
        $this->assertDatabaseHas('exchange_rates', ['currency' => 'GBP', 'source' => 'frankfurter']);
        $this->assertDatabaseMissing('exchange_rates', ['currency' => 'XXX']);
    }

    public function test_failed_refresh_keeps_the_last_stored_rate(): void
    {
        Http::fake(['api.frankfurter.dev/*' => Http::response('bad gateway', 502)]);
        $fx = app(ExchangeRateService::class);

        $this->assertSame(0, $fx->refresh());
        $this->assertEqualsWithDelta(75.605, $fx->convert(100, 'USD', 'GBP'), 0.001);
    }

    public function test_command_reports_failure_without_touching_rates(): void
    {
        Http::fake(['api.frankfurter.dev/*' => Http::response([], 500)]);

        $this->artisan('fx:refresh')->assertFailed();
        $this->assertSame(0.75605, (float) ExchangeRate::where('currency', 'GBP')->value('rate'));
    }

    public function test_staleness_is_flagged(): void
    {
        $fx = app(ExchangeRateService::class);
        ExchangeRate::query()->update(['fetched_at' => now()->subDays(3)]);
        $this->assertTrue($fx->isStale());

        ExchangeRate::query()->update(['fetched_at' => now()]);
        $this->assertFalse($fx->isStale());
    }

    public function test_helpers_convert_into_the_users_wallet_currency(): void
    {
        $gbp = User::factory()->create(['currency' => 'GBP']);
        $usd = User::factory()->create(['currency' => 'USD']);

        $this->assertSame('£75.61', money_from(100, 'USD', 2, $gbp));
        $this->assertSame('$100.00', money_from(100, 'USD', 2, $usd));
        $this->assertEqualsWithDelta(75.605, $gbp->convertFrom(100), 0.001);
    }
}
