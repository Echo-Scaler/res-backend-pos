<?php

namespace App\Http\Requests\Order;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTableOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Public tableside self-ordering is validated by the table's unique cryptographic qr_token in the route
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'items.*.special_notes' => ['nullable', 'string', 'max:255'],
            'customer_notes' => ['nullable', 'string', 'max:255'],
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
            'items.required' => 'Please select at least one dish from the menu to place an order.',
            'items.min' => 'Please select at least one dish from the menu to place an order.',
            'items.*.product_id.required' => 'A valid dish selection is required.',
            'items.*.product_id.exists' => 'One or more selected dishes could not be found.',
            'items.*.quantity.min' => 'Dish quantity must be at least 1.',
        ];
    }
}
