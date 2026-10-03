<?php

namespace App\Http\Requests\Pos;

use Illuminate\Foundation\Http\FormRequest;

class RequestBillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'table_id' => ['required', 'exists:dining_tables,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'table_id.required' => 'စားပွဲခုံ ရွေးချယ်ပေးရန် လိုအပ်ပါသည်။',
        ];
    }
}
