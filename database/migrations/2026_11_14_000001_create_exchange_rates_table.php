<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->char('base', 3)->default('USD');
            $table->char('currency', 3);
            $table->decimal('rate', 18, 8);          // 1 base = rate currency
            $table->string('source', 30)->default('frankfurter');
            $table->timestamp('fetched_at')->nullable();
            $table->timestamps();

            $table->unique(['base', 'currency']);
        });

        // Starting values (ECB, 2026-11-13) so conversion works before the first refresh.
        $seed = ['EUR' => 0.90481, 'GBP' => 0.75605, 'JPY' => 160.0, 'CAD' => 1.4258, 'AUD' => 1.4738,
            'CHF' => 0.8384, 'CNY' => 6.6719, 'INR' => 94.25, 'BRL' => 5.6637];

        foreach ($seed as $currency => $rate) {
            DB::table('exchange_rates')->insert([
                'base' => 'USD', 'currency' => $currency, 'rate' => $rate, 'source' => 'seed',
                'fetched_at' => '2026-11-13 00:00:00', 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
