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
            'destination.bank_name' => 'required_without:destination.wallet_address|string|max:255',
            'destination.account_name' => 'required_without:destination.wallet_address|string|max:255',
            'destination.account_number' => 'required_without:destination.wallet_address|string|max:64',
            'destination.wallet_address' => 'required_without:destination.bank_name|string|max:255',
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
