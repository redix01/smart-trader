<?php

namespace App\Services;

use App\Models\MarketPair;
use App\Models\User;
use App\Models\SwapQuote;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class SwapService
{
    private const FEES_PERCENT = 0.05;

    public function __construct(
        private WalletService $wallets,
        private UserNotificationService $notifications,
    ) {}

    public function getSupportedPairs(): array
    {
        $prices = $this->getReferencePrices();
        $currencies = array_keys($prices);
        $pairs = [];

        foreach ($currencies as $from) {
            foreach ($currencies as $to) {
                if ($from === $to) {
                    continue;
                }

                $pairs[] = [
                    'from' => $from,
                    'to' => $to,
                    'rate' => $prices[$from] / $prices[$to],
                ];
            }
        }

        return $pairs;
    }

    public function getAvailableCurrencies(User $user): array
    {
        return $user->wallets()
            ->orderByDesc('balance')
            ->get()
            ->filter(fn ($wallet) => (float) $wallet->balance > 0 || in_array($wallet->currency, ['USD', 'USDT'], true))
            ->pluck('currency')
            ->unique()
            ->values()
            ->all();
    }

    public function getUserWalletBalances(User $user): array
    {
        return $user->wallets()
            ->orderBy('currency')
            ->get()
            ->map(fn ($wallet) => [
                'symbol' => $wallet->currency,
                'balance' => (float) $wallet->balance,
                'available' => (float) $wallet->available_balance,
            ])
            ->values()
            ->all();
    }

    public function getQuote(string $from, string $to, float $amount): ?array
    {
        $pairs = $this->getSupportedPairs();
        $pair = collect($pairs)->first(fn ($candidate) => $candidate['from'] === strtoupper($from) && $candidate['to'] === strtoupper($to));

        if (! $pair) {
            return null;
        }

        $toAmount = $amount * $pair['rate'];
        $fee = $toAmount * (self::FEES_PERCENT / 100);

        return [
            'from_currency' => strtoupper($from),
            'to_currency' => strtoupper($to),
            'from_amount' => $amount,
            'to_amount' => $toAmount - $fee,
            'rate' => $pair['rate'],
            'fee' => $fee,
            'fee_percent' => self::FEES_PERCENT,
        ];
    }

    public function executeSwap(User $user, string $from, string $to, float $amount): ?SwapQuote
    {
        $quote = $this->getQuote($from, $to, $amount);

        if (! $quote) {
            return null;
        }

        return DB::transaction(function () use ($user, $quote) {
            $this->wallets->debit($user, $quote['from_currency'], (float) $quote['from_amount']);
            $this->wallets->credit($user, $quote['to_currency'], (float) $quote['to_amount']);

            $swap = SwapQuote::create([
                'user_id' => $user->id,
                'from_currency' => $quote['from_currency'],
                'to_currency' => $quote['to_currency'],
                'from_amount' => $quote['from_amount'],
                'to_amount' => $quote['to_amount'],
                'rate' => $quote['rate'],
                'fee' => $quote['fee'],
                'status' => 'completed',
            ]);

            DB::afterCommit(fn () => $this->notifications->sendSwapCompleted($user, $swap));

            return $swap;
        });
    }

    public function getUserSwaps(User $user): Collection
    {
        return $user->swapQuotes()
            ->orderByDesc('created_at')
            ->get()
            ->toBase()
            ->map(fn (SwapQuote $q) => [
                'id' => $q->id,
                'from' => $q->from_currency,
                'to' => $q->to_currency,
                'from_amount' => number_format((float) $q->from_amount, 6),
                'to_amount' => number_format((float) $q->to_amount, 6),
                'rate' => $q->rate,
                'status' => $q->status,
                'date' => $q->created_at->format('Y-m-d H:i'),
            ]);
    }

    private function getReferencePrices(): array
    {
        $pairs = MarketPair::where('is_active', true)
            ->where('quote_currency', 'USDT')
            ->get()
            ->keyBy('base_currency');

        return collect($pairs)
            ->mapWithKeys(fn (MarketPair $pair) => [$pair->base_currency => (float) $pair->current_price])
            ->prepend(1.0, 'USDT')
            ->prepend(1.0, 'USD')
            ->all();
    }
}
