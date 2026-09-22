<?php

namespace App\Http\Requests\Admin\Promotions;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePromotionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user && ($user->hasRole('OWNER') || $user->hasRole('MANAGER') || $user->can('manage-promotions'));
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('code')) {
            $this->merge([
                'code' => strtoupper(trim((string) $this->input('code'))),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $currentUser = $this->user();

        return [
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('promotions', 'code')->where(function ($query) use ($currentUser) {
                    $query->where('restaurant_id', $currentUser->restaurant_id);
                }),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'type' => ['required', 'string', Rule::in(['PERCENTAGE', 'FIXED'])],
            'value' => ['required', 'numeric', 'min:0.01'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'max_discount_amount' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
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
            'code.required' => 'Coupon code is required (e.g. WELCOME10).',
            'code.unique' => 'This coupon code already exists in your restaurant.',
            'name.required' => 'Promotion campaign name is required.',
            'type.in' => 'Discount type must be either PERCENTAGE or FIXED amount.',
            'value.required' => 'Discount percentage or amount value is required.',
            'end_date.after_or_equal' => 'Promotion expiration date must be after or equal to the start date.',
        ];
    }
}
