<?php

namespace App\Console\Commands;

use App\Services\ExchangeRateService;
use Illuminate\Console\Command;

class RefreshExchangeRates extends Command
{
    protected $signature = 'fx:refresh';

    protected $description = 'Refresh USD exchange rates used to show prices in each user\'s currency';

    public function handle(ExchangeRateService $fx): int
    {
        $updated = $fx->refresh();

        if ($updated === 0) {
            $this->error('No rates updated; keeping the last stored rates.');

            return self::FAILURE;
        }

        $this->info("Updated {$updated} exchange rates.");

        return self::SUCCESS;
    }
}
