<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Expert extends Model
{
    /** @use HasFactory<ExpertFactory> */
    use HasFactory;

    protected $fillable = [
        'name', 'avatar', 'win_rate', 'profit_share',
        'status', 'total_volume', 'bio', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'win_rate' => 'decimal:2',
            'profit_share' => 'decimal:2',
            'total_volume' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(CopySubscription::class);
    }
}
