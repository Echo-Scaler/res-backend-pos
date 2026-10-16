<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ConfirmTwoFactorRequest extends FormRequest
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
            'code' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
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
            'code.required' => 'အတည်ပြုရန် 6-digit OTP code ထည့်သွင်းပေးရန် လိုအပ်ပါသည်။ (6-digit OTP code is required.)',
            'code.size' => 'OTP code သည် ဂဏန်း ၆ လုံး အတိအကျ ဖြစ်ရပါမည်။ (Code must be exactly 6 digits.)',
            'code.regex' => 'OTP code သည် ဂဏန်း ၆ လုံးသာ ဖြစ်ရပါမည်။ (Code must be numeric digits.)',
        ];
    }
}
