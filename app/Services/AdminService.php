<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\KycSubmission;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class AdminService
{
    public function __construct(private UserNotificationService $notifications) {}

    public function getPendingKyc(): Collection
    {
        return KycSubmission::where('status', 'pending')
            ->with('user')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (KycSubmission $k) => [
                'id' => $k->id,
                'user_name' => $k->user?->name,
                'user_email' => $k->user?->email,
                'document_type' => $k->id_document_type,
                'submitted_at' => $k->created_at->format('Y-m-d H:i'),
            ]);
    }

    public function approveKyc(int $submissionId, int $reviewerId): void
    {
        $submission = KycSubmission::findOrFail($submissionId);
        $submission->update([
            'status' => 'approved',
            'reviewed_at' => now(),
            'reviewed_by' => $reviewerId,
        ]);

        $submission->user->update(['kyc_level' => 'verified']);
        $this->notifications->sendKycApproved($submission->user, $submission->fresh());
    }

    public function rejectKyc(int $submissionId, int $reviewerId, string $reason): void
    {
        $submission = KycSubmission::findOrFail($submissionId);
        $submission->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'reviewed_at' => now(),
            'reviewed_by' => $reviewerId,
        ]);

        $this->notifications->sendKycRejected($submission->user, $submission->fresh());
    }

    public function getPendingDeposits(): Collection
    {
        return Deposit::where('status', 'pending')
            ->with('user', 'depositMethod')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Deposit $d) => [
                'id' => $d->id,
                'user_name' => $d->user?->name,
                'method' => $d->depositMethod?->label,
                'amount' => number_format((float) $d->amount, 2),
                'currency' => $d->currency,
                'has_proof' => !is_null($d->proof_path),
                'submitted_at' => $d->created_at->format('Y-m-d H:i'),
            ]);
    }

    public function approveDeposit(int $depositId, int $reviewerId): void
    {
        DB::transaction(function () use ($depositId, $reviewerId) {
            $deposit = Deposit::whereKey($depositId)
                ->lockForUpdate()
                ->with('user')
                ->firstOrFail();

            if ($deposit->status === 'approved') {
                return;
            }

            $deposit->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => $reviewerId,
            ]);

            $wallet = $deposit->user->wallets()->firstOrCreate(
                ['currency' => $deposit->currency],
                [
                    'label' => $deposit->currency,
                    'balance' => 0,
                    'locked_balance' => 0,
                    'is_active' => true,
                ]
            );

            $wallet->increment('balance', (float) $deposit->net_amount);

            DB::afterCommit(fn () => $this->notifications->sendDepositApproved($deposit->user, $deposit->fresh()));
        });
    }

    public function rejectDeposit(int $depositId, int $reviewerId, string $reason): void
    {
        $deposit = Deposit::findOrFail($depositId);
        $deposit->update([
            'status' => 'rejected',
            'admin_notes' => $reason,
            'approved_at' => now(),
            'approved_by' => $reviewerId,
        ]);

        $this->notifications->sendDepositRejected($deposit->user, $deposit->fresh());
    }

    public function getPendingWithdrawals(): Collection
    {
        return Withdrawal::where('status', 'pending')
            ->with('user')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Withdrawal $w) => [
                'id' => $w->id,
                'user_name' => $w->user?->name,
                'method' => $w->method,
                'amount' => number_format((float) $w->amount, 2),
                'currency' => $w->currency,
                'submitted_at' => $w->created_at->format('Y-m-d H:i'),
            ]);
    }

    public function approveWithdrawal(int $withdrawalId, int $reviewerId): void
    {
        DB::transaction(function () use ($withdrawalId, $reviewerId) {
            $withdrawal = Withdrawal::whereKey($withdrawalId)
                ->lockForUpdate()
                ->with('user')
                ->firstOrFail();

            if ($withdrawal->status === 'approved') {
                return;
            }

            abort_if($withdrawal->status !== 'pending', 422, 'Only pending withdrawals can be approved.');

            $withdrawal->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => $reviewerId,
            ]);

            DB::afterCommit(fn () => $this->notifications->sendWithdrawalApproved($withdrawal->user, $withdrawal->fresh()));
        });
    }

    public function rejectWithdrawal(int $withdrawalId, int $reviewerId, string $reason): void
    {
        DB::transaction(function () use ($withdrawalId, $reviewerId, $reason) {
            $withdrawal = Withdrawal::whereKey($withdrawalId)
                ->lockForUpdate()
                ->with('user')
                ->firstOrFail();

            if ($withdrawal->status === 'rejected') {
                return;
            }

            abort_if($withdrawal->status !== 'pending', 422, 'Only pending withdrawals can be rejected.');

            $withdrawal->update([
                'status' => 'rejected',
                'admin_notes' => $reason,
                'approved_at' => now(),
                'approved_by' => $reviewerId,
            ]);

            $wallet = $withdrawal->user->wallets()->firstOrCreate(
                ['currency' => $withdrawal->currency],
                [
                    'label' => $withdrawal->currency,
                    'balance' => 0,
                    'locked_balance' => 0,
                    'is_active' => true,
                ]
            );

            $wallet->increment('balance', (float) $withdrawal->amount);

            DB::afterCommit(fn () => $this->notifications->sendWithdrawalRejected($withdrawal->user, $withdrawal->fresh()));
        });
    }
}
