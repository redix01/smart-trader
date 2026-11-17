<?php

namespace App\Http\Controllers\Admin;

use App\Events\DepositApproved;
use App\Events\WithdrawalApproved;
use App\Http\Controllers\Controller;
use App\Mail\DeclineDepositMail;
use App\Mail\DepositApprovalMail;
use App\Mail\ApproveWithdrawalMail;
use App\Mail\RejectWithdrawalMail;
use App\Models\Deposit;
use App\Models\User;
use App\Models\Withdrawal;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    /**
     * Display all deposits with user and payment method relationships
     */
    public function deposits(Request $request)
    {
        $filterUser = $request->filled('user') ? User::find($request->query('user')) : null;
        $deposits = Deposit::with(['user', 'payment_method'])
                          ->when($filterUser, fn ($q) => $q->where('user_id', $filterUser->id))
                          ->latest()
                          ->get();
        return view('admin.transactions.deposits', compact('deposits', 'filterUser'));
    }

    /**
     * Get deposit details for modal view
     */
    public function getDepositDetails($id)
    {
        try {
            $deposit = Deposit::with(['user', 'payment_method'])
                             ->findOrFail($id);

            $html = view('admin.partials.deposit-details', compact('deposit'))->render();

            return response()->json([
                'success' => true,
                'html' => $html
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load deposit details'
            ]);
        }
    }

    /**
     * Wallet column a deposit is credited to. The user picks the wallet on the deposit form
     * (balance / trading / holding / staking); each maps to its own users.* column.
     */
    public const WALLET_COLUMNS = [
        'balance' => 'balance',
        'trading' => 'trading_balance',
        'holding' => 'holding_balance',
        'staking' => 'staking_balance',
    ];

    /**
     * Approve a deposit and credit the wallet the user chose. Locks the deposit row so a double
     * click (or two admins) can never credit twice.
     */
    public function approveDeposit($id)
    {
        try {
            $deposit = DB::transaction(function () use ($id) {
                $deposit = Deposit::whereKey($id)->lockForUpdate()->firstOrFail();
                if ((int) $deposit->status !== 0) {
                    return null;
                }

                $user = User::whereKey($deposit->user_id)->lockForUpdate()->firstOrFail();
                $column = self::WALLET_COLUMNS[$deposit->wallet_type] ?? 'balance';
                $user->increment($column, (float) $deposit->amount);

                $deposit->status = 1;
                $deposit->save();

                return $deposit;
            });

            if (! $deposit) {
                return redirect()->back()->with('error', 'Deposit has already been processed.');
            }

            $user = $deposit->user;

            // Process referral reward for the user's first approved deposit
            try {
                app(ReferralService::class)->processDepositReward($user, $deposit);
            } catch (\Throwable $e) {
                \Log::error('Referral reward processing failed: ' . $e->getMessage());
            }

            $user->createNotification(
                'deposit_approved',
                'Deposit Approved',
                "Your deposit of " . $user->formatAmount($deposit->amount) . " to your " . ucfirst($deposit->wallet_type) . " wallet has been approved and credited to your account.",
                [
                    'amount' => $deposit->amount,
                    'wallet_type' => $deposit->wallet_type,
                    'deposit_id' => $deposit->id,
                    'status' => 'approved'
                ]
            );

            try {
                Mail::to($user->email)->send(new DepositApprovalMail($deposit));
            } catch (\Throwable $e) {
                \Log::error('Failed to send deposit approval email: ' . $e->getMessage());
            }

            return redirect()->back()->with('success', 'Deposit approved successfully! User account has been credited.');

        } catch (\Throwable $e) {
            \Log::error('Deposit approval failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to approve deposit. Please try again.');
        }
    }

    /**
     * Decline a deposit (nothing was credited, so nothing to reverse)
     */
    public function declineDeposit($id)
    {
        try {
            $deposit = DB::transaction(function () use ($id) {
                $deposit = Deposit::with('user')->whereKey($id)->lockForUpdate()->firstOrFail();
                if ((int) $deposit->status !== 0) {
                    return null;
                }
                $deposit->status = 2;
                $deposit->save();

                return $deposit;
            });

            if (! $deposit) {
                return redirect()->back()->with('error', 'Deposit has already been processed.');
            }

            $deposit->user?->createNotification(
                'deposit_declined',
                'Deposit Declined',
                "Your deposit of " . $deposit->user->formatAmount($deposit->amount) . " could not be verified and was declined. Please contact support if you believe this is a mistake.",
                ['amount' => $deposit->amount, 'wallet_type' => $deposit->wallet_type, 'deposit_id' => $deposit->id, 'status' => 'declined']
            );

            try {
                if ($deposit->user) {
                    Mail::to($deposit->user->email)->send(new DeclineDepositMail($deposit));
                }
            } catch (\Throwable $e) {
                \Log::error('Failed to send deposit decline email: ' . $e->getMessage());
            }

            return redirect()->back()->with('success', 'Deposit declined successfully.');

        } catch (\Throwable $e) {
            \Log::error('Deposit decline failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to decline deposit. Please try again.');
        }
    }

    /**
     * Delete a deposit
     */
    public function deleteDeposit($id)
    {
        try {
            $deposit = Deposit::findOrFail($id);

            // Delete proof file if it exists
            if ($deposit->proof && Storage::disk('public')->exists($deposit->proof)) {
                Storage::disk('public')->delete($deposit->proof);
            }

            $deposit->delete();

            return redirect()->back()->with('success', 'Deposit deleted successfully.');

        } catch (\Exception $e) {
            \Log::error('Deposit deletion failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete deposit. Please try again.');
        }
    }

    /**
     * Display all withdrawals
     */
    public function withdrawal()
    {
        $withdrawal = Withdrawal::with('user')->latest()->get();
        return view('admin.transactions.withdrawal', compact('withdrawal'));
    }

    /**
     * Display all withdrawals (alias for withdrawal)
     */
    public function withdrawals(Request $request)
    {
        $filterUser = $request->filled('user') ? User::find($request->query('user')) : null;
        $withdrawal = Withdrawal::with('user')
            ->when($filterUser, fn ($q) => $q->where('user_id', $filterUser->id))
            ->latest()->get();
        return view('admin.transactions.withdrawal', compact('withdrawal', 'filterUser'));
    }



    /**
     * Approve a withdrawal. The amount was already taken from the user's wallet when they
     * requested it, so approval only marks it paid.
     */
    public function approveWithdrawal(Withdrawal $withdrawal)
    {
        try {
            $approved = DB::transaction(function () use ($withdrawal) {
                $w = Withdrawal::whereKey($withdrawal->getKey())->lockForUpdate()->firstOrFail();
                if ((int) $w->status !== 0) {
                    return null;
                }
                $w->status = 1;
                $w->save();

                return $w;
            });

            if (! $approved) {
                return redirect()->back()->with('error', 'Withdrawal has already been processed.');
            }

            $approved->user?->createNotification(
                'withdrawal_approved',
                'Withdrawal Approved',
                "Your withdrawal request of " . $approved->user->formatAmount($approved->amount) . " has been approved and will be processed shortly.",
                [
                    'amount' => $approved->amount,
                    'withdrawal_id' => $approved->id,
                    'status' => 'approved'
                ]
            );

            try {
                if ($approved->user) {
                    Mail::to($approved->user->email)->send(new ApproveWithdrawalMail($approved));
                }
            } catch (\Throwable $e) {
                \Log::error('Failed to send withdrawal approval email: ' . $e->getMessage());
            }

            return redirect()->back()->with('success', 'Withdrawal approved successfully.');

        } catch (\Throwable $e) {
            \Log::error('Withdrawal approval failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to approve withdrawal. Please try again.');
        }
    }

    /**
     * Reject a withdrawal and refund the wallet it was taken from (balance, trading_balance
     * or mining_balance, as chosen on the user's withdrawal form). Refunds once only.
     */
    public function rejectWithdrawal(Withdrawal $withdrawal)
    {
        try {
            $rejected = DB::transaction(function () use ($withdrawal) {
                $w = Withdrawal::whereKey($withdrawal->getKey())->lockForUpdate()->firstOrFail();
                if ((int) $w->status !== 0) {
                    return null;
                }

                $from = in_array($w->from_account, ['balance', 'trading_balance', 'mining_balance'], true) ? $w->from_account : 'balance';
                User::whereKey($w->user_id)->lockForUpdate()->first()?->increment($from, (float) $w->amount);

                $w->status = 2;
                $w->save();

                return $w;
            });

            if (! $rejected) {
                return redirect()->back()->with('error', 'Withdrawal has already been processed.');
            }

            $rejected->user?->createNotification(
                'withdrawal_rejected',
                'Withdrawal Rejected',
                "Your withdrawal request of " . $rejected->user->formatAmount($rejected->amount) . " was rejected and the funds were returned to your " . str_replace('_', ' ', $rejected->from_account ?: 'balance') . ".",
                ['amount' => $rejected->amount, 'withdrawal_id' => $rejected->id, 'status' => 'rejected']
            );

            try {
                if ($rejected->user) {
                    Mail::to($rejected->user->email)->send(new RejectWithdrawalMail($rejected));
                }
            } catch (\Throwable $e) {
                \Log::error('Failed to send withdrawal decline email: ' . $e->getMessage());
            }

            return redirect()->back()->with('success', 'Withdrawal rejected and the funds were returned to the user.');

        } catch (\Throwable $e) {
            \Log::error('Withdrawal reject failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to reject withdrawal. Please try again.');
        }
    }
}
