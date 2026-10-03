<?php

namespace App\Http\Requests\Pos;

use Illuminate\Foundation\Http\FormRequest;

class OpenShiftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'terminal_code' => ['required', 'string', 'max:50'],
            'opening_float' => ['required', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'terminal_code.required' => 'Terminal code (ဥပမာ- POS-01) ထည့်သွင်းပေးရန် လိုအပ်ပါသည်။',
            'opening_float.required' => 'အစပြု ငွေသား float (MMK) ထည့်သွင်းပေးရန် လိုအပ်ပါသည်။',
            'opening_float.min' => 'အစပြု ငွေသားပမာဏသည် အနည်းဆုံး ၀ ကျပ် ဖြစ်ရပါမည်။',
        ];
    }
}
