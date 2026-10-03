<?php

namespace App\Http\Requests\Pos;

use Illuminate\Foundation\Http\FormRequest;

class CashInOutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:CASH_IN,CASH_OUT'],
            'amount' => ['required', 'integer', 'min:100'],
            'reason' => ['required', 'string', 'min:3', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.in' => 'ငွေသွင်း/ငွေထုတ် အမျိုးအစား မှန်ကန်မှု မရှိပါ။',
            'amount.required' => 'ငွေပမာဏ (MMK) ထည့်သွင်းပေးရန် လိုအပ်ပါသည်။',
            'amount.min' => 'ငွေပမာဏသည် အနည်းဆုံး ၁၀၀ ကျပ် ဖြစ်ရပါမည်။',
            'reason.required' => 'ငွေသွင်း/ထုတ်ရသည့် အကြောင်းပြချက် ထည့်သွင်းပေးရန် လိုအပ်ပါသည်။',
        ];
    }
}
