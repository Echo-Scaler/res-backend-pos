<?php

namespace App\Http\Requests\Admin\Order;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifyOrderItemsRequest extends FormRequest
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
            'action' => ['required', 'string', Rule::in(['VERIFY_AND_DELIVER', 'MARK_READY', 'TOGGLE_ITEM'])],
            'item_ids' => ['nullable', 'array'],
            'item_ids.*' => ['integer', 'exists:order_items,id'],
            'item_id' => ['nullable', 'integer', 'exists:order_items,id'],
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
            'action.required' => 'Verification action is required.',
            'action.in' => 'Invalid order verification action specified.',
        ];
    }
}
