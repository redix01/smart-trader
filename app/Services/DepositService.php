<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\DepositMethod;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class DepositService
{
    public function getActiveMethods(): Collection
    {
        return DepositMethod::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn (DepositMethod $m) => [
                'id' => $m->id,
                'currency' => $m->currency,
                'network' => $m->network,
                'name' => $m->label,
                'address' => $m->wallet_address,
                'icon' => $m->icon,
                'min_amount' => $m->min_amount,
                'max_amount' => $m->max_amount,
            ]);
    }

    public function createDeposit(User $user, array $data, ?UploadedFile $proof = null): Deposit
    {
        $method = DepositMethod::findOrFail($data['deposit_method_id']);
        $amount = (float) $data['amount'];
        $fee = $amount * ((float) $method->fee_percent / 100) + (float) $method->fee_fixed;

        $deposit = Deposit::create([
            'user_id' => $user->id,
            'deposit_method_id' => $method->id,
            'amount' => $amount,
            'fee' => $fee,
            'net_amount' => $amount - $fee,
            'currency' => $method->currency,
            'status' => 'pending',
        ]);

        if ($proof) {
            $path = $proof->store('deposits/' . $user->id, 'public');
            $deposit->update([
                'proof_path' => $path,
                'proof_mime' => $proof->getMimeType(),
            ]);
        }

        return $deposit;
    }

    public function getUserDeposits(User $user): Collection
    {
        return $user->deposits()
            ->with('depositMethod')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Deposit $d) => [
                'id' => $d->id,
                'method' => $d->depositMethod?->label ?? 'Unknown',
                'currency' => $d->currency,
                'amount' => number_format((float) $d->amount, 2),
                'fee' => number_format((float) $d->fee, 2),
                'net' => number_format((float) $d->net_amount, 2),
                'status' => $d->status,
                'date' => $d->created_at->format('Y-m-d H:i'),
            ]);
    }
}
