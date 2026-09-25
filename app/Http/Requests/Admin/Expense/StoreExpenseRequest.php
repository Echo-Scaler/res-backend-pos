<?php

namespace App\Http\Requests\Admin\Expense;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create-expenses') ?? true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:expense_categories,id'],
            'category' => ['nullable', 'string', 'max:100'],
            'vendor_id' => ['nullable', 'exists:vendors,id'],
            'amount' => ['required', 'integer', 'min:0'],
            'tax_amount' => ['nullable', 'integer', 'min:0'],
            'expense_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'reason' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'submit_now' => ['nullable', 'boolean'],
            'receipt' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf,webp', 'max:10240'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Expense title is required.',
            'amount.required' => 'Expense amount (in MMK) is required.',
            'amount.integer' => 'Expense amount must be a valid MMK integer.',
            'expense_date.required' => 'Expense date is required.',
            'receipt.max' => 'Receipt attachment size cannot exceed 10MB.',
        ];
    }
}
