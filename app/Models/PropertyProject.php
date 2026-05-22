<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PropertyProject extends Model
{
    /** @use HasFactory<PropertyProjectFactory> */
    use HasFactory;

    protected $fillable = [
        'title', 'region', 'description', 'strategy',
        'min_investment', 'target_roi', 'status',
        'image', 'media', 'disclosure', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'min_investment' => 'decimal:2',
            'target_roi' => 'decimal:2',
            'media' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function investments(): HasMany
    {
        return $this->hasMany(PropertyInvestment::class);
    }
}
