<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRestaurantSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole('OWNER') ?? false;
    }

    /**
     * Prepare inputs before validation (converting checkboxes to booleans).
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'show_logo_on_receipt' => $this->boolean('show_logo_on_receipt'),
            'show_tax_id_on_receipt' => $this->boolean('show_tax_id_on_receipt'),
            'is_dine_in_enabled' => $this->boolean('is_dine_in_enabled'),
            'is_takeaway_enabled' => $this->boolean('is_takeaway_enabled'),
            'is_qr_order_enabled' => $this->boolean('is_qr_order_enabled'),
            'auto_kick_drawer' => $this->boolean('auto_kick_drawer'),
            'auto_print_receipt' => $this->boolean('auto_print_receipt'),
            'remove_logo' => $this->boolean('remove_logo'),
            'remove_cover' => $this->boolean('remove_cover'),
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
            'name' => ['required', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'secondary_phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'tax_id' => ['nullable', 'string', 'max:100'],
            'receipt_header' => ['nullable', 'string', 'max:1000'],
            'receipt_footer' => ['nullable', 'string', 'max:1000'],
            'show_logo_on_receipt' => ['boolean'],
            'show_tax_id_on_receipt' => ['boolean'],
            'opening_time' => ['nullable'],
            'closing_time' => ['nullable'],
            'operating_days' => ['nullable', 'array'],
            'operating_days.*' => ['string', 'in:MON,TUE,WED,THU,FRI,SAT,SUN'],
            'is_dine_in_enabled' => ['boolean'],
            'is_takeaway_enabled' => ['boolean'],
            'is_qr_order_enabled' => ['boolean'],
            'default_tax_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'default_service_charge_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'printer_paper_width' => ['nullable', 'string', 'in:80mm,58mm'],
            'auto_kick_drawer' => ['boolean'],
            'auto_print_receipt' => ['boolean'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'remove_logo' => ['boolean'],
            'remove_cover' => ['boolean'],
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
            'name.required' => 'ဆိုင်အမည် (Restaurant Name) ဖြည့်သွင်းရန် လိုအပ်ပါသည်။',
            'logo.max' => 'ဆိုင်တံဆိပ် Logo ဖိုင်ဆိုဒ်သည် 2MB ထက် မကျော်လွန်ရပါ။',
            'cover_image.max' => 'Cover ပုံရိပ် ဖိုင်ဆိုဒ်သည် 4MB ထက် မကျော်လွန်ရပါ။',
            'default_tax_percent.max' => 'အခွန်ရာခိုင်နှုန်းသည် 100% ထက် မကျော်လွန်ရပါ။',
            'default_service_charge_percent.max' => 'ဝန်ဆောင်ခရာခိုင်နှုန်းသည် 100% ထက် မကျော်လွန်ရပါ။',
        ];
    }
}
