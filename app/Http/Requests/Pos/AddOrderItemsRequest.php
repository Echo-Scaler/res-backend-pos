<?php

namespace App\Http\Requests\Pos;

use Illuminate\Foundation\Http\FormRequest;

class AddOrderItemsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'items.*.special_notes' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'ထပ်မံဖြည့်စွက်မည့် ဟင်းလျာ ရွေးချယ်ပေးရန် လိုအပ်ပါသည်။',
            'items.min' => 'အနည်းဆုံး ဟင်းလျာ ၁ မျိုး ရွေးချယ်ပေးရန် လိုအပ်ပါသည်။',
        ];
    }
}
