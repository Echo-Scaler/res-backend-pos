<?php

namespace App\Http\Requests\Pos;

use Illuminate\Foundation\Http\FormRequest;

class StoreTablesideOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'table_id' => ['required', 'exists:dining_tables,id'],
            'guest_count' => ['required', 'integer', 'min:1', 'max:50'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'items.*.special_notes' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'table_id.required' => 'စားပွဲခုံ ရွေးချယ်ပေးရန် လိုအပ်ပါသည်။',
            'guest_count.min' => 'ဧည့်သည် အရေအတွက်သည် အနည်းဆုံး ၁ ယောက် ဖြစ်ရပါမည်။',
            'items.required' => 'အနည်းဆုံး ဟင်းလျာ ၁ မျိုး ရွေးချယ်ပေးရန် လိုအပ်ပါသည်။',
            'items.min' => 'အနည်းဆုံး ဟင်းလျာ ၁ မျိုး ရွေးချယ်ပေးရန် လိုအပ်ပါသည်။',
        ];
    }
}
