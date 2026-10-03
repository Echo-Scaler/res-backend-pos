<?php

namespace App\Http\Requests\Pos;

use Illuminate\Foundation\Http\FormRequest;

class SettleOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'payment_method' => ['required', 'string', 'in:CASH,KBZPAY,WAVEPAY,CARD,SPLIT'],
            'discount_type' => ['nullable', 'string', 'in:NONE,PERCENT,FIXED'],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'amount_tendered' => ['nullable', 'integer', 'min:0'],
            'reference_no' => ['nullable', 'string', 'max:100'],
            'split_cash_amount' => ['nullable', 'integer', 'min:0'],
            'split_digital_amount' => ['nullable', 'integer', 'min:0'],
            'split_digital_method' => ['nullable', 'string', 'in:KBZPAY,WAVEPAY,CARD'],
            'split_digital_ref' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.required' => 'ငွေပေးချေမှုပုံစံ (Cash, KBZPay, WavePay, Card) ရွေးချယ်ပေးရန် လိုအပ်ပါသည်။',
            'payment_method.in' => 'ခွင့်မပြုထားသော ငွေပေးချေမှုစနစ် ဖြစ်ပါသည်။',
        ];
    }
}
