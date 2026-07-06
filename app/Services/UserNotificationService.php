<?php

namespace App\Services;

use App\Mail\UserActionMail;
use App\Models\CopySubscription;
use App\Models\Deposit;
use App\Models\KycSubmission;
use App\Models\MiningSubscription;
use App\Models\Order;
use App\Models\PropertyInvestment;
use App\Models\Stake;
use App\Models\SwapQuote;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\Mail;
use Throwable;

class UserNotificationService
{
    public function __construct(private PlatformSettingsService $platformSettings) {}

    public function sendWelcome(User $user): void
    {
        $this->sendToUser(
            $user,
            'Welcome to your account',
            'Your account is ready',
            'Your account has been created successfully. You can now complete verification, fund your wallet, and start using the platform.',
            [
                'Name' => $user->name,
                'Email' => $user->email,
                'Created' => now()->format('Y-m-d H:i'),
            ],
            'Open Dashboard',
            route('dashboard')
        );
    }

    public function sendAdminNewRegistration(User $user): void
    {
        $this->sendToAdmin(
            'New user registration',
            'A new user has registered',
            'A new account was created and may require review from the admin team.',
            [
                'Name' => $user->name,
                'Email' => $user->email,
                'Registered at' => $user->created_at?->format('Y-m-d H:i') ?? now()->format('Y-m-d H:i'),
            ],
            'View Users',
            route('admin.users.index')
        );
    }

    public function sendKycSubmitted(User $user, KycSubmission $submission): void
    {
        $this->sendToAdmin(
            'New KYC submission',
            'A user submitted KYC documents',
            'A KYC submission is pending admin review.',
            [
                'User' => $user->name,
                'Email' => $user->email,
                'Document type' => strtoupper($submission->id_document_type),
                'Submitted at' => $submission->created_at?->format('Y-m-d H:i') ?? now()->format('Y-m-d H:i'),
                'Status' => strtoupper($submission->status),
            ],
            'Review KYC',
            route('admin.kyc.index')
        );
    }

    public function sendKycApproved(User $user, KycSubmission $submission): void
    {
        $this->sendToUser(
            $user,
            'KYC approved',
            'Your identity has been verified',
            'Your KYC review has been approved. Verified features are now available on your account.',
            [
                'Document type' => strtoupper($submission->id_document_type),
                'Reviewed at' => $submission->reviewed_at?->format('Y-m-d H:i') ?? now()->format('Y-m-d H:i'),
                'Status' => 'APPROVED',
            ],
            'Open Dashboard',
            route('dashboard')
        );
    }

    public function sendKycRejected(User $user, KycSubmission $submission): void
    {
        $details = [
            'Document type' => strtoupper($submission->id_document_type),
            'Reviewed at' => $submission->reviewed_at?->format('Y-m-d H:i') ?? now()->format('Y-m-d H:i'),
            'Status' => 'REJECTED',
        ];

        if ($submission->rejection_reason) {
            $details['Reason'] = $submission->rejection_reason;
        }

        $this->sendToUser(
            $user,
            'KYC review update',
            'Your KYC submission needs attention',
            'Your KYC submission was not approved. Review the reason below and submit updated documents.',
            $details,
            'Resubmit KYC',
            route('kyc')
        );
    }

    public function sendDepositSubmitted(User $user, Deposit $deposit, string $methodLabel): void
    {
        $this->sendToAdmin(
            'New deposit request',
            'A deposit request needs review',
            'A user submitted a deposit request that is waiting for admin approval.',
            [
                'User' => $user->name,
                'Email' => $user->email,
                'Amount' => $this->formatAmount((float) $deposit->amount, $deposit->currency),
                'Fee' => $this->formatAmount((float) $deposit->fee, $deposit->currency),
                'Net amount' => $this->formatAmount((float) $deposit->net_amount, $deposit->currency),
                'Currency' => strtoupper($deposit->currency),
                'Method' => $methodLabel,
                'Status' => strtoupper($deposit->status),
            ],
            'Review Deposits',
            route('admin.deposits.index')
        );
    }

    public function sendDepositApproved(User $user, Deposit $deposit): void
    {
        $this->sendToUser(
            $user,
            'Deposit approved',
            'Your deposit has been credited',
            'Your deposit was approved and the net amount has been added to your wallet balance.',
            [
                'Amount' => $this->formatAmount((float) $deposit->amount, $deposit->currency),
                'Credited' => $this->formatAmount((float) $deposit->net_amount, $deposit->currency),
                'Currency' => strtoupper($deposit->currency),
                'Approved at' => $deposit->approved_at?->format('Y-m-d H:i') ?? now()->format('Y-m-d H:i'),
                'Status' => 'APPROVED',
            ],
            'Open Dashboard',
            route('dashboard')
        );
    }

    public function sendDepositRejected(User $user, Deposit $deposit): void
    {
        $details = [
            'Amount' => $this->formatAmount((float) $deposit->amount, $deposit->currency),
            'Currency' => strtoupper($deposit->currency),
            'Status' => 'REJECTED',
        ];

        if ($deposit->admin_notes) {
            $details['Reason'] = $deposit->admin_notes;
        }

        $this->sendToUser(
            $user,
            'Deposit review update',
            'Your deposit request was rejected',
            'Your deposit request could not be approved. Review the details below and submit a new request if needed.',
            $details,
            'View Deposits',
            route('deposit')
        );
    }

    public function sendWithdrawalSubmitted(User $user, Withdrawal $withdrawal): void
    {
        $this->sendToUser(
            $user,
            'Withdrawal request submitted',
            'Your withdrawal request has been received',
            'Your withdrawal request was submitted successfully and is now pending admin approval.',
            [
                'Amount' => $this->formatAmount((float) $withdrawal->amount, $withdrawal->currency),
                'Fee' => $this->formatAmount((float) $withdrawal->fee, $withdrawal->currency),
                'Net amount' => $this->formatAmount((float) $withdrawal->net_amount, $withdrawal->currency),
                'Currency' => strtoupper($withdrawal->currency),
                'Method' => strtoupper($withdrawal->method),
                'Status' => strtoupper($withdrawal->status),
            ],
            'View Withdrawals',
            route('withdraw')
        );

        $this->sendToAdmin(
            'New withdrawal request',
            'A withdrawal request needs review',
            'A user submitted a withdrawal request that is waiting for admin approval.',
            [
                'User' => $user->name,
                'Email' => $user->email,
                'Amount' => $this->formatAmount((float) $withdrawal->amount, $withdrawal->currency),
                'Fee' => $this->formatAmount((float) $withdrawal->fee, $withdrawal->currency),
                'Net amount' => $this->formatAmount((float) $withdrawal->net_amount, $withdrawal->currency),
                'Currency' => strtoupper($withdrawal->currency),
                'Method' => strtoupper($withdrawal->method),
                'Status' => strtoupper($withdrawal->status),
            ],
            'Review Withdrawals',
            route('admin.withdrawals.index')
        );
    }

    public function sendWithdrawalApproved(User $user, Withdrawal $withdrawal): void
    {
        $this->sendToUser(
            $user,
            'Withdrawal approved',
            'Your withdrawal is on its way',
            "We've reviewed and approved your withdrawal request. The funds below are now being sent to the destination you provided.",
            [
                'Amount requested' => $this->formatAmount((float) $withdrawal->amount, $withdrawal->currency),
                'Amount you will receive' => $this->formatAmount((float) $withdrawal->net_amount, $withdrawal->currency),
                'Currency' => strtoupper($withdrawal->currency),
                'Approved on' => $withdrawal->approved_at?->format('Y-m-d H:i') ?? now()->format('Y-m-d H:i'),
                'Status' => 'Approved',
            ],
            'View your withdrawals',
            route('withdraw')
        );
    }

    public function sendWithdrawalRejected(User $user, Withdrawal $withdrawal): void
    {
        $details = [
            'Amount' => $this->formatAmount((float) $withdrawal->amount, $withdrawal->currency),
            'Currency' => strtoupper($withdrawal->currency),
            'Status' => 'REJECTED',
        ];

        if ($withdrawal->admin_notes) {
            $details['Reason'] = $withdrawal->admin_notes;
        }

        $this->sendToUser(
            $user,
            'Withdrawal review update',
            'Your withdrawal request was rejected',
            'Your withdrawal request was not approved. Review the details below and contact support if you need help.',
            $details,
            'View Withdrawals',
            route('withdraw')
        );
    }

    public function sendSwapCompleted(User $user, SwapQuote $swap): void
    {
        $this->sendToUser(
            $user,
            'Swap completed',
            'Your asset swap was completed',
            'Your swap has been processed successfully and your wallet balances have been updated.',
            [
                'From' => $this->formatAmount((float) $swap->from_amount, $swap->from_currency),
                'To' => $this->formatAmount((float) $swap->to_amount, $swap->to_currency),
                'Rate' => number_format((float) $swap->rate, 8, '.', ''),
                'Fee' => $this->formatAmount((float) $swap->fee, $swap->to_currency),
                'Status' => strtoupper($swap->status),
            ],
            'View Swap History',
            route('swap')
        );
    }

    public function sendTradeCompleted(User $user, Order $order): void
    {
        $this->sendToUser(
            $user,
            'Trade executed',
            'Your order was completed',
            'Your trade has been executed successfully and your wallet balances were updated.',
            [
                'Pair' => strtoupper($order->pair),
                'Side' => strtoupper($order->side),
                'Order type' => strtoupper($order->type),
                'Amount' => $this->formatNumber((float) $order->amount),
                'Price' => $this->formatNumber((float) $order->price),
                'Total' => $this->formatNumber((float) $order->total),
                'Fee' => $this->formatNumber((float) $order->fee),
                'Status' => strtoupper($order->status),
            ],
            'View Trading Page',
            route('trades', ['asset' => explode('/', $order->pair)[0]])
        );
    }

    public function sendStakeCreated(User $user, Stake $stake, string $planName, string $currency): void
    {
        $this->sendToUser(
            $user,
            'Stake created',
            'Your staking position is active',
            'Your stake has been created successfully and your wallet balance was updated.',
            [
                'Plan' => $planName,
                'Amount' => $this->formatAmount((float) $stake->amount, $currency),
                'Currency' => strtoupper($currency),
                'Start date' => $stake->start_date?->format('Y-m-d H:i') ?? now()->format('Y-m-d H:i'),
                'End date' => $stake->end_date?->format('Y-m-d H:i') ?? 'N/A',
                'Status' => strtoupper($stake->status),
            ],
            'View Staking',
            route('stakes')
        );
    }

    public function sendMiningSubscriptionCreated(User $user, MiningSubscription $subscription, string $planName): void
    {
        $this->sendToUser(
            $user,
            'Mining subscription created',
            'Your mining plan is active',
            'Your mining subscription has been created successfully and your wallet balance was updated.',
            [
                'Plan' => $planName,
                'Amount' => $this->formatAmount((float) $subscription->amount, 'USD'),
                'Start date' => $subscription->start_date?->format('Y-m-d H:i') ?? now()->format('Y-m-d H:i'),
                'End date' => $subscription->end_date?->format('Y-m-d H:i') ?? 'N/A',
                'Status' => strtoupper($subscription->status),
            ],
            'View Mining',
            route('mining')
        );
    }

    public function sendExpertSubscriptionCreated(User $user, CopySubscription $subscription, string $expertName): void
    {
        $this->sendToUser(
            $user,
            'Expert subscription created',
            'Your copy-trading allocation is active',
            'Your expert subscription has been created successfully.',
            [
                'Expert' => $expertName,
                'Allocation' => $this->formatAmount((float) $subscription->allocation_amount, 'USD'),
                'Status' => strtoupper($subscription->status),
            ],
            'View Experts',
            route('experts')
        );
    }

    public function sendPropertyInvestmentCreated(User $user, PropertyInvestment $investment, string $projectTitle): void
    {
        $this->sendToUser(
            $user,
            'Real estate investment submitted',
            'Your property investment was created',
            'Your investment has been recorded successfully.',
            [
                'Project' => $projectTitle,
                'Amount' => $this->formatAmount((float) $investment->amount, 'USD'),
                'Expected return' => $this->formatAmount((float) $investment->expected_return, 'USD'),
                'Status' => strtoupper($investment->status),
            ],
            'View Investments',
            route('realestate')
        );
    }

    private function sendToUser(
        User $user,
        string $subject,
        string $heading,
        string $message,
        array $details = [],
        ?string $actionLabel = null,
        ?string $actionUrl = null,
    ): void {
        $this->deliver($user->email, $user->name, $subject, $heading, $message, $details, $actionLabel, $actionUrl);
    }

    private function sendToAdmin(
        string $subject,
        string $heading,
        string $message,
        array $details = [],
        ?string $actionLabel = null,
        ?string $actionUrl = null,
    ): void {
        $this->deliver(
            $this->platformSettings->getAdminMailAddress(),
            $this->platformSettings->getAdminMailName(),
            $subject,
            $heading,
            $message,
            $details,
            $actionLabel,
            $actionUrl
        );
    }

    private function deliver(
        string $email,
        ?string $name,
        string $subject,
        string $heading,
        string $message,
        array $details = [],
        ?string $actionLabel = null,
        ?string $actionUrl = null,
    ): void {
        rescue(function () use ($email, $name, $subject, $heading, $message, $details, $actionLabel, $actionUrl) {
            Mail::to($email, $name)->send(new UserActionMail(
                subjectLine: $subject,
                heading: $heading,
                messageLine: $message,
                details: $details,
                actionLabel: $actionLabel,
                actionUrl: $actionUrl,
            ));
        }, function (Throwable $e) {
            report($e);
        });
    }

    private function formatAmount(float $amount, string $currency): string
    {
        return $this->formatNumber($amount) . ' ' . strtoupper($currency);
    }

    private function formatNumber(float $value): string
    {
        $formatted = number_format($value, 8, '.', ',');

        return rtrim(rtrim($formatted, '0'), '.');
    }
}
