<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketPair extends Model
{
    protected $fillable = [
        'base_currency', 'quote_currency', 'current_price', 'price_change_24h',
        'volume_24h', 'high_24h', 'low_24h', 'market_cap', 'icon', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'current_price' => 'decimal:8',
            'price_change_24h' => 'decimal:8',
            'volume_24h' => 'decimal:2',
            'high_24h' => 'decimal:8',
            'low_24h' => 'decimal:8',
            'market_cap' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function watchlists(): HasMany
    {
        return $this->hasMany(Watchlist::class);
    }

    public function getNameAttribute(): string
    {
        return "{$this->base_currency}/{$this->quote_currency}";
    }
}
