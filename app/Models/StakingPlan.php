<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StakingPlan extends Model
{
    /** @use HasFactory<StakingPlanFactory> */
    use HasFactory;

    protected $fillable = [
        'name', 'currency', 'icon', 'min_amount', 'max_amount',
        'apy', 'payout_cycle', 'duration_days', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'min_amount' => 'decimal:8',
            'max_amount' => 'decimal:8',
            'apy' => 'decimal:2',
            'duration_days' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function stakes(): HasMany
    {
        return $this->hasMany(Stake::class);
    }
}
