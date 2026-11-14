<?php

use App\Models\User;

if (! function_exists('currency_symbol')) {
    /** Symbol for a user's chosen currency (defaults to the signed-in user, then $). */
    function currency_symbol(?User $user = null): string
    {
        $user ??= auth()->user();

        return $user?->currency_symbol ?? '$';
    }
}

if (! function_exists('money')) {
    /**
     * Format an amount with the user's currency symbol, e.g. £1,250.00.
     * Amounts are stored in the user's own currency, so no conversion happens here.
     * Pass $user for emails/admin contexts where the signed-in user is not the owner.
     */
    function money($amount, int $decimals = 2, ?User $user = null): string
    {
        return currency_symbol($user).number_format((float) $amount, $decimals);
    }
}

if (! function_exists('convert_currency')) {
    /** Convert between currencies using the stored exchange rates. */
    function convert_currency($amount, string $from, string $to): float
    {
        return app(\App\Services\ExchangeRateService::class)->convert($amount, $from, $to);
    }
}

if (! function_exists('to_user_currency')) {
    /**
     * Convert a platform/market price (USD by default) into the user's wallet currency.
     * Wallet amounts themselves (balances, deposits, trades) are already in the user's currency.
     */
    function to_user_currency($amount, ?string $from = 'USD', ?User $user = null): float
    {
        $user ??= auth()->user();

        return convert_currency($amount, $from ?: 'USD', $user?->currency ?: 'USD');
    }
}

if (! function_exists('money_from')) {
    /** Format a USD (or other-currency) price converted into the user's currency, e.g. 100 USD -> £75.61. */
    function money_from($amount, ?string $from = 'USD', int $decimals = 2, ?User $user = null): string
    {
        return money(to_user_currency($amount, $from, $user), $decimals, $user);
    }
}
