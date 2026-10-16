<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePinRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string', 'current_password'],
            'pin_code' => ['required', 'numeric', 'digits_between:4,6', 'confirmed'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'current_password.required' => 'အတည်ပြုရန် လက်ရှိ စကားဝှက် ထည့်ပေးရန် လိုအပ်ပါသည်။ (Current password is required.)',
            'current_password.current_password' => 'လက်ရှိ စကားဝှက် မှားယွင်းနေပါသည်။ (Current password is incorrect.)',
            'pin_code.required' => 'POS Master PIN အသစ် ထည့်သွင်းပေးရန် လိုအပ်ပါသည်။ (Master PIN is required.)',
            'pin_code.numeric' => 'Master PIN သည် ဂဏန်းသီးသန့်သာ ဖြစ်ရပါမည်။ (PIN must be numeric only.)',
            'pin_code.digits_between' => 'Master PIN သည် အနည်းဆုံး ၄ လုံးမှ အများဆုံး ၆ လုံးအတွင်း ဖြစ်ရပါမည်။ (PIN must be between 4 and 6 digits.)',
            'pin_code.confirmed' => 'အတည်ပြု PIN နံပါတ်နှင့် ကိုက်ညီမှု မရှိပါ။ (PIN confirmation does not match.)',
        ];
    }
}
