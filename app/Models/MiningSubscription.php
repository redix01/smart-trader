<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MiningSubscription extends Model
{
    protected $fillable = [
        'user_id', 'mining_plan_id', 'amount', 'earned_so_far',
        'status', 'start_date', 'end_date',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'earned_so_far' => 'decimal:2',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function miningPlan(): BelongsTo
    {
        return $this->belongsTo(MiningPlan::class);
    }
}
