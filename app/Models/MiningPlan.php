<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MiningPlan extends Model
{
    /** @use HasFactory<MiningPlanFactory> */
    use HasFactory;

    protected $fillable = [
        'name', 'min_amount', 'max_amount', 'roi_percent',
        'duration_days', 'icon', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'min_amount' => 'decimal:2',
            'max_amount' => 'decimal:2',
            'roi_percent' => 'decimal:2',
            'duration_days' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(MiningSubscription::class);
    }
}
