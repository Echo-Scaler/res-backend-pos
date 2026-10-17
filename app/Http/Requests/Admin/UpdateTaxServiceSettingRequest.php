<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaxServiceSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user) {
            return false;
        }

        return $user->hasRole('OWNER') || $user->hasRole('MANAGER') || $user->can('manage_settings');
    }

    /**
     * Prepare inputs before validation (converting checkboxes and numbers to appropriate types).
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_tax_enabled' => $this->boolean('is_tax_enabled'),
            'apply_tax_dine_in' => $this->boolean('apply_tax_dine_in'),
            'apply_tax_takeaway' => $this->boolean('apply_tax_takeaway'),
            'apply_tax_delivery' => $this->boolean('apply_tax_delivery'),
            'apply_tax_qr_order' => $this->boolean('apply_tax_qr_order'),
            'is_service_charge_enabled' => $this->boolean('is_service_charge_enabled'),
            'is_service_charge_taxable' => $this->boolean('is_service_charge_taxable'),
            'service_charge_dine_in_only' => $this->boolean('service_charge_dine_in_only'),
            'round_cash_only' => $this->boolean('round_cash_only'),
            'show_tax_breakdown_on_receipt' => $this->boolean('show_tax_breakdown_on_receipt'),
            'show_service_charge_on_receipt' => $this->boolean('show_service_charge_on_receipt'),
            'print_tax_stamp_seal' => $this->boolean('print_tax_stamp_seal'),
            'takeaway_packaging_fee' => (int) ($this->input('takeaway_packaging_fee') ?? 0),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Commercial Tax Rules
            'is_tax_enabled' => ['required', 'boolean'],
            'tax_name' => ['required', 'string', 'max:100'],
            'default_tax_percent' => ['required', 'numeric', 'min:0', 'max:30'],
            'tax_calculation_mode' => ['required', 'string', 'in:inclusive,exclusive'],
            'tax_id' => ['nullable', 'string', 'max:100'],
            'tax_office_name' => ['nullable', 'string', 'max:150'],
            'apply_tax_dine_in' => ['required', 'boolean'],
            'apply_tax_takeaway' => ['required', 'boolean'],
            'apply_tax_delivery' => ['required', 'boolean'],
            'apply_tax_qr_order' => ['required', 'boolean'],

            // Dining Service Charge Rules
            'is_service_charge_enabled' => ['required', 'boolean'],
            'service_charge_name' => ['required', 'string', 'max:100'],
            'default_service_charge_percent' => ['required', 'numeric', 'min:0', 'max:25'],
            'is_service_charge_taxable' => ['required', 'boolean'],
            'service_charge_dine_in_only' => ['required', 'boolean'],
            'takeaway_packaging_fee' => ['required', 'integer', 'min:0', 'max:100000'],

            // Currency & Rounding Rules (Rule 6: MMK)
            'rounding_method' => ['required', 'string', 'in:none,round_50,round_100'],
            'rounding_direction' => ['required', 'string', 'in:nearest,ceil,floor'],
            'round_cash_only' => ['required', 'boolean'],

            // Thermal Receipt Settings
            'show_tax_breakdown_on_receipt' => ['required', 'boolean'],
            'show_service_charge_on_receipt' => ['required', 'boolean'],
            'print_tax_stamp_seal' => ['required', 'boolean'],
            'tax_disclaimer_text' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'default_tax_percent.min' => 'အခွန် ရာခိုင်နှုန်းသည် အနည်းဆုံး ၀% ဖြစ်ရပါမည်။',
            'default_tax_percent.max' => 'အခွန် ရာခိုင်နှုန်းသည် အများဆုံး ၃၀% ထက် မကျော်လွန်ရပါ။',
            'default_service_charge_percent.min' => 'ဝန်ဆောင်ခ ရာခိုင်နှုန်းသည် အနည်းဆုံး ၀% ဖြစ်ရပါမည်။',
            'default_service_charge_percent.max' => 'ဝန်ဆောင်ခ ရာခိုင်နှုန်းသည် အများဆုံး ၂၅% ထက် မကျော်လွန်ရပါ။',
            'tax_calculation_mode.in' => 'အခွန်တွက်ချက်မှု ပုံစံသည် Inclusive သို့မဟုတ် Exclusive သာ ဖြစ်ရပါမည်။',
            'rounding_method.in' => 'ငွေကြေးညှိနှိုင်းမှု ပုံစံ မှန်ကန်မှု မရှိပါ။',
            'takeaway_packaging_fee.min' => 'ပါဆယ်ဘူးခသည် ၀ ကျပ်ထက် မနည်းရပါ။',
        ];
    }
}
