<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'method' => 'required|string|max:64',
            'amount' => 'required|numeric|min:1|max:1000000',
            'currency' => 'sometimes|string|size:3',
            'destination' => 'required|array',
            'destination.bank_name' => 'required_without:destination.wallet_address|string|max:255',
            'destination.account_name' => 'required_without:destination.wallet_address|string|max:255',
            'destination.account_number' => 'required_without:destination.wallet_address|string|max:64',
            'destination.wallet_address' => 'required_without:destination.bank_name|string|max:255',
        ];
    }
}
