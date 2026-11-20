<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Commands
|--------------------------------------------------------------------------
| Here you can define custom console commands for your application.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

/*
|--------------------------------------------------------------------------
| Scheduled Tasks
|--------------------------------------------------------------------------
| All scheduled tasks for the application should be defined here.
| These will be executed by the Laravel scheduler via cron job.
|
*/

// Trade Management
Schedule::call(function () {
    $controller = new \App\Http\Controllers\TradeController;
    $controller->checkTradeDuration();
})->everyMinute();

// Real-time Price Updates
Schedule::command('prices:update-scheduled')->everyThirtySeconds();

// Asset Price Updates (Crypto & Stocks)
Schedule::command('assets:update-prices')->everyFiveMinutes();

// Trade Room order matching: fill reached limit orders, apply take-profit / stop-loss / liquidation.
// Single firing path (this scheduler); overlap-guarded; idempotent under row locks.
Schedule::command('trades:match')->everyMinute()->withoutOverlapping(5);

// Exchange rates (USD base) for valuing Trade Room prices in each user's currency
Schedule::command('fx:refresh')->everySixHours()->withoutOverlapping();

// Bot Trading Simulation
Schedule::command('bot-trading:simulate')->everyMinute();

// Trade Market Simulation
Schedule::command('trade:simulate-market')->everyFiveMinutes();
