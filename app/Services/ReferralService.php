<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\Referral;
use App\Models\User;

class ReferralService
{
    /**
     * Commission rate applied to a referred user's first approved deposit.
     */
    public const COMMISSION_RATE = 0.05;

    /**
     * Reward the referrer when a referred user's first deposit is approved.
     *
     * @return Referral|null The created referral record, or null if no reward applies.
     */
    public function processDepositReward(User $user, Deposit $deposit): ?Referral
    {
        if (!$user->referred_by || $deposit->amount <= 0) {
            return null;
        }

        // Only reward the first approved deposit for this referred user.
        $alreadyRewarded = Referral::where('referred_user_id', $user->id)
            ->where('status', 'paid')
            ->exists();

        if ($alreadyRewarded) {
            return null;
        }

        $referrer = $user->referrer;
        if (!$referrer) {
            return null;
        }

        $commission = $deposit->amount * self::COMMISSION_RATE;
        if ($commission <= 0) {
            return null;
        }

        $record = Referral::create([
            'referrer_id' => $referrer->id,
            'referred_user_id' => $user->id,
            'amount' => $commission,
            'status' => 'pending',
            'description' => "Referral reward from {$user->name}'s first deposit of {$user->formatAmount($deposit->amount)}.",
        ]);

        $referrer->addToBalance($commission, 'referral');

        $record->update([
            'status' => 'paid',
            'rewarded_at' => now(),
        ]);

        $referrer->createNotification(
            'referral_reward',
            'Referral Reward Received',
            "You earned {$referrer->formatAmount($commission)} for referring {$user->name}. Keep sharing your link!",
            [
                'amount' => $commission,
                'referred_user_id' => $user->id,
                'referred_user_name' => $user->name,
                'referral_id' => $record->id,
            ]
        );

        return $record;
    }
}
