<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'account_tier', 'kyc_level',
        'locale', 'currency', 'avatar', 'last_login_at',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function wallets(): HasMany
    {
        return $this->hasMany(Wallet::class);
    }

    public function watchlists(): HasMany
    {
        return $this->hasMany(Watchlist::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class);
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function swapQuotes(): HasMany
    {
        return $this->hasMany(SwapQuote::class);
    }

    public function stakes(): HasMany
    {
        return $this->hasMany(Stake::class);
    }

    public function miningSubscriptions(): HasMany
    {
        return $this->hasMany(MiningSubscription::class);
    }

    public function copySubscriptions(): HasMany
    {
        return $this->hasMany(CopySubscription::class);
    }

    public function propertyInvestments(): HasMany
    {
        return $this->hasMany(PropertyInvestment::class);
    }

    public function kycSubmissions(): HasMany
    {
        return $this->hasMany(KycSubmission::class);
    }

    public function isAdmin(): bool
    {
        return $this->account_tier === 'admin';
    }

    public function isKycVerified(): bool
    {
        return $this->kyc_level === 'verified';
    }
}
