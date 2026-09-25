<?php

namespace App\Http\Requests\Admin\Report;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ExportReportRequest extends FormRequest
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
            'format' => ['nullable', 'string', 'in:csv,print'],
            'tab' => ['nullable', 'string', 'in:overview,sales,cogs,pnl,alcohol,tables,ordertypes,voids,promotions,comparison,audit'],
            'period' => ['nullable', 'string', 'in:today,yesterday,this_week,this_month,last_month,this_year,custom'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }
}
