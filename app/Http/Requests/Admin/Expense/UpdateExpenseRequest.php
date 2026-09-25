<?php

namespace App\Http\Requests\Admin\Expense;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('edit-expenses') ?? true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:expense_categories,id'],
            'vendor_id' => ['nullable', 'exists:vendors,id'],
            'amount' => ['sometimes', 'required', 'integer', 'min:0'],
            'tax_amount' => ['nullable', 'integer', 'min:0'],
            'expense_date' => ['sometimes', 'required', 'date'],
            'due_date' => ['nullable', 'date'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'reason' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'submit_now' => ['nullable', 'boolean'],
            'receipt' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf,webp', 'max:10240'],
        ];
    }
}
