<?php

namespace App\Http\Requests\Admin\Expense;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PayExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('pay-expenses') ?? true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'payment_method' => ['required', 'string', 'in:CASH,BANK_TRANSFER,CREDIT_CARD,DEBIT_CARD,KBZPAY,WAVEPAY,AYA_PAY,OTHER'],
            'payment_date' => ['nullable', 'date'],
            'payment_reference' => ['nullable', 'string', 'max:100'],
            'account_or_drawer_name' => ['nullable', 'string', 'max:150'],
            'amount' => ['nullable', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
