<?php

namespace App\Http\Requests\Admin\Expense;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBudgetRequest extends FormRequest
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
            'category_id' => ['required', 'exists:expense_categories,id'],
            'fiscal_year' => ['required', 'integer', 'min:2020', 'max:2100'],
            'period_type' => ['required', 'string', 'in:MONTHLY,QUARTERLY,YEARLY'],
            'period_month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'budget_amount' => ['required', 'integer', 'min:0'],
            'alert_threshold_percent' => ['nullable', 'integer', 'min:1', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
