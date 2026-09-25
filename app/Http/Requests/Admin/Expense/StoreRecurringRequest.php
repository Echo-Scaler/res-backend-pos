<?php

namespace App\Http\Requests\Admin\Expense;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRecurringRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-budgets') ?? true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:expense_categories,id'],
            'vendor_id' => ['nullable', 'exists:vendors,id'],
            'amount' => ['required', 'integer', 'min:0'],
            'frequency' => ['required', 'string', 'in:DAILY,WEEKLY,MONTHLY,QUARTERLY,YEARLY'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'next_due_date' => ['required', 'date'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'auto_submit' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
