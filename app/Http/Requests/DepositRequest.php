<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepositRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'deposit_method_id' => 'required|exists:deposit_methods,id',
            'amount' => 'required|numeric|min:1|max:1000000',
            'proof' => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'proof.max' => 'Proof file must not exceed 5MB.',
            'proof.mimes' => 'Proof must be a PNG, JPG, JPEG, or PDF file.',
        ];
    }
}
