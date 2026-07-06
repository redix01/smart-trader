<?php

namespace App\Http\Requests;

use App\Services\PlatformSettingsService;
use Illuminate\Foundation\Http\FormRequest;

class WithdrawalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $settings = app(PlatformSettingsService::class);
        $minWithdrawal = max(0.00000001, $settings->getFloat('min_withdrawal', 5));
        $maxWithdrawal = max($minWithdrawal, $settings->getFloat('max_withdrawal', 50000));

        return [
            'method' => 'required|string|max:64',
            'amount' => 'required|numeric|min:' . $minWithdrawal . '|max:' . $maxWithdrawal,
            'currency' => 'sometimes|string|size:3',
            'destination' => 'required|array',
            'destination.bank_name' => 'nullable|required_without_all:destination.wallet_address,destination.paypal_email|string|max:255',
            'destination.account_name' => 'nullable|required_without_all:destination.wallet_address,destination.paypal_email|string|max:255',
            'destination.account_number' => 'nullable|required_without_all:destination.wallet_address,destination.paypal_email|string|max:64',
            'destination.wallet_address' => 'nullable|required_without_all:destination.bank_name,destination.paypal_email|string|max:255',
            'destination.paypal_email' => 'nullable|required_without_all:destination.bank_name,destination.wallet_address|email|max:255',
            'destination.routing_number' => 'nullable|string|max:64',
        ];
    }

    public function messages(): array
    {
        $settings = app(PlatformSettingsService::class);
        $minWithdrawal = max(0.00000001, $settings->getFloat('min_withdrawal', 5));
        $maxWithdrawal = max($minWithdrawal, $settings->getFloat('max_withdrawal', 50000));

        return [
            'amount.min' => 'The withdrawal amount must be at least ' . number_format($minWithdrawal, 2) . '.',
            'amount.max' => 'The withdrawal amount must not exceed ' . number_format($maxWithdrawal, 2) . '.',
        ];
    }
}
