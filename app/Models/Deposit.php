<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Deposit extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = ['user_id', 'amount', 'payment_method_id', 'proof', 'status', 'wallet_type'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function getStatusBadgeAttribute()
    {
        if ($this->status == 1)
        {
            return '<span class="badge bg-success">Successful</span>';
        }
        if ($this->status == 2)
        {
            return '<span class="badge bg-danger">Declined</span>';
        }
        return '<span class="badge bg-warning">Pending</span>';
    }

    public function adminStatus()
    {
        if ($this->status == 1)
        {
            return '<span class="bg-green-100 text-green-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded-md dark:bg-gray-700 dark:text-green-400 border border-green-100 dark:border-green-500">Approved</span>';
        }
        if ($this->status == 2)
        {
            return '<span class="bg-red-100 text-red-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded-md dark:bg-gray-700 dark:text-red-400 border border-red-100 dark:border-red-500">Declined</span>';
        }
        return '<span class="bg-orange-100 text-orange-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded-md border border-orange-100 dark:bg-gray-700 dark:border-orange-300 dark:text-orange-300">Pending</span>';

    }
}
