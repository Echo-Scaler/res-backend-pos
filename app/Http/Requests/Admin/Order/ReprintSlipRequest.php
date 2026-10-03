<?php

namespace App\Http\Requests\Admin\Order;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReprintSlipRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user && ($user->hasRole('OWNER') || $user->hasRole('MANAGER') || $user->hasRole('CASHIER') || $user->hasRole('STAFF'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'slip_type' => ['required', 'string', Rule::in(['KITCHEN_CHIT', 'CUSTOMER_BILL', 'BOTH'])],
            'reason' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Custom validation error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'slip_type.required' => 'Slip type to reproduce is required (Kitchen Chit, Customer Bill, or Both).',
            'slip_type.in' => 'Invalid slip type specified.',
        ];
    }
}
