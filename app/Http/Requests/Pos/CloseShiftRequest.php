<?php

namespace App\Http\Requests\Pos;

use Illuminate\Foundation\Http\FormRequest;

class CloseShiftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'closing_actual_cash' => ['required', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'closing_actual_cash.required' => 'အံဆွဲအတွင်းရှိ အမှန်တကယ် လက်ကျန်ငွေသား (MMK) ရေတွက်ထည့်သွင်းပေးရန် လိုအပ်ပါသည်။',
            'closing_actual_cash.min' => 'လက်ကျန်ငွေသားပမာဏသည် အနည်းဆုံး ၀ ကျပ် ဖြစ်ရပါမည်။',
        ];
    }
}
