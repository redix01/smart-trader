<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::when($request->search, fn($q, $v) => $q->where('name', 'like', "%{$v}%")->orWhere('email', 'like', "%{$v}%"))
            ->when($request->tier, fn($q, $v) => $q->where('account_tier', $v))
            ->when($request->kyc, fn($q, $v) => $q->where('kyc_level', $v))
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'tier', 'kyc']),
        ]);
    }

    public function show(User $user)
    {
        $user->load(['wallets', 'kycSubmissions', 'deposits' => fn($q) => $q->latest()->limit(10), 'withdrawals' => fn($q) => $q->latest()->limit(10)]);
        $user->makeVisible('plain_password');

        return Inertia::render('Admin/Users/Show', ['user' => $user]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:users,email,'.$user->id,
            'account_tier' => 'sometimes|in:user,admin',
            'kyc_level' => 'sometimes|in:unverified,pending,verified,rejected',
        ]);

        $user->update($data);
        return redirect()->back()->with('success', 'User updated.');
    }

    public function adjustWallet(Request $request, User $user)
    {
        $data = $request->validate([
            'currency' => 'required|string|max:10',
            'operation' => 'required|in:add,subtract',
            'amount' => 'required|numeric|min:0.00000001',
            'note' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($data, $request, $user) {
            $currency = strtoupper($data['currency']);
            $amount = (float) $data['amount'];

            $wallet = $user->wallets()
                ->where('currency', $currency)
                ->lockForUpdate()
                ->first();

            if (! $wallet) {
                if ($data['operation'] === 'subtract') {
                    throw ValidationException::withMessages([
                        'currency' => "User does not have a {$currency} wallet.",
                    ]);
                }

                $wallet = $user->wallets()->create([
                    'currency' => $currency,
                    'label' => $currency,
                    'balance' => 0,
                    'locked_balance' => 0,
                    'is_active' => true,
                ]);
            }

            $before = (float) $wallet->balance;
            $after = $data['operation'] === 'add'
                ? $before + $amount
                : $before - $amount;

            if ($after < 0) {
                throw ValidationException::withMessages([
                    'amount' => "Insufficient {$currency} balance.",
                ]);
            }

            $wallet->forceFill(['balance' => $after])->save();

            Transaction::create([
                'transactionable_type' => $wallet->getMorphClass(),
                'transactionable_id' => $wallet->id,
                'user_id' => $user->id,
                'type' => $data['operation'] === 'add' ? 'admin_credit' : 'admin_debit',
                'currency' => $currency,
                'amount' => $amount,
                'fee' => 0,
                'balance_before' => $before,
                'balance_after' => $after,
                'status' => 'completed',
                'description' => ($data['note'] ?? null) ?: 'Manual admin wallet adjustment by admin #'.$request->user()->id,
            ]);
        });

        return redirect()->back()->with('success', 'Wallet balance updated.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }
}
