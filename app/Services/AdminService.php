<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\KycSubmission;
use App\Models\Withdrawal;
use Illuminate\Support\Collection;

class AdminService
{
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
        $deposit = Deposit::findOrFail($depositId);
        $deposit->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $reviewerId,
        ]);
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
        $withdrawal = Withdrawal::findOrFail($withdrawalId);
        $withdrawal->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $reviewerId,
        ]);
    }

    public function rejectWithdrawal(int $withdrawalId, int $reviewerId, string $reason): void
    {
        $withdrawal = Withdrawal::findOrFail($withdrawalId);
        $withdrawal->update([
            'status' => 'rejected',
            'admin_notes' => $reason,
            'approved_at' => now(),
            'approved_by' => $reviewerId,
        ]);
    }
}
