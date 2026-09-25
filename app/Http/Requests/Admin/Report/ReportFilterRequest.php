<?php

namespace App\Http\Requests\Admin\Report;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ReportFilterRequest extends FormRequest
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
            'tab' => ['nullable', 'string', 'in:overview,sales,orders,cogs,pnl,alcohol,tables,ordertypes,voids,promotions,expenses,comparison,forecast,audit'],
            'period' => ['nullable', 'string', 'in:today,yesterday,this_week,this_month,last_month,this_year,custom'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'interval' => ['nullable', 'string', 'in:daily,weekly,monthly,yearly'],
            'category_id' => ['nullable', 'integer'],
            'employee_id' => ['nullable', 'integer'],
            'order_type' => ['nullable', 'string', 'in:DINE_IN,TAKEAWAY,DELIVERY,PICKUP'],
            'payment_method' => ['nullable', 'string', 'in:CASH,KBZPAY,WAVEPAY,CARD'],
        ];
    }
}
