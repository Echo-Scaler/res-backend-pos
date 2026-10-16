<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
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
            'password' => ['required', 'string', Password::min(8)->letters()->numbers(), 'confirmed'],
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
            'current_password.required' => 'လက်ရှိ စကားဝှက်ကို ထည့်သွင်းပေးရန် လိုအပ်ပါသည်။ (Current password is required.)',
            'current_password.current_password' => 'လက်ရှိ စကားဝှက် မှားယွင်းနေပါသည်။ (Current password is incorrect.)',
            'password.required' => 'စကားဝှက်အသစ် ထည့်သွင်းပေးရန် လိုအပ်ပါသည်။ (New password is required.)',
            'password.min' => 'စကားဝှက်အသစ်သည် အနည်းဆုံး စာလုံး ၈ လုံး ရှိရပါမည်။ (Password must be at least 8 characters.)',
            'password.confirmed' => 'အတည်ပြုစကားဝှက်နှင့် ကိုက်ညီမှု မရှိပါ။ (Password confirmation does not match.)',
        ];
    }
}
