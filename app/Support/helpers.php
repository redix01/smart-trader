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
