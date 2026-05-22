<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stake extends Model
{
    protected $fillable = [
        'user_id', 'staking_plan_id', 'amount', 'accrued_rewards',
        'status', 'start_date', 'end_date', 'last_payout_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:8',
            'accrued_rewards' => 'decimal:8',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'last_payout_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stakingPlan(): BelongsTo
    {
        return $this->belongsTo(StakingPlan::class);
    }
}
