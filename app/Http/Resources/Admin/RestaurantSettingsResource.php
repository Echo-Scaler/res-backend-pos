<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantSettingsResource extends JsonResource
{
    /**
     * Transform the restaurant configuration into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'tagline' => $this->tagline,
            'logo_url' => $this->logo_url,
            'cover_image_url' => $this->cover_image_url,
            'phone' => $this->phone,
            'secondary_phone' => $this->secondary_phone,
            'email' => $this->email,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'tax_id' => $this->tax_id,
            'receipt_header' => $this->receipt_header,
            'receipt_footer' => $this->receipt_footer,
            'show_logo_on_receipt' => (bool) $this->show_logo_on_receipt,
            'show_tax_id_on_receipt' => (bool) $this->show_tax_id_on_receipt,
            'opening_time' => $this->opening_time,
            'closing_time' => $this->closing_time,
            'operating_days' => $this->operating_days ?? ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'],
            'is_dine_in_enabled' => (bool) $this->is_dine_in_enabled,
            'is_takeaway_enabled' => (bool) $this->is_takeaway_enabled,
            'is_qr_order_enabled' => (bool) $this->is_qr_order_enabled,
            'currency' => 'MMK',
            'currency_symbol' => 'Ks ',
            'default_tax_percent' => (float) $this->default_tax_percent,
            'default_service_charge_percent' => (float) $this->default_service_charge_percent,
            'printer_paper_width' => $this->printer_paper_width ?? '80mm',
            'auto_kick_drawer' => (bool) $this->auto_kick_drawer,
            'auto_print_receipt' => (bool) $this->auto_print_receipt,
            'is_active' => (bool) $this->is_active,
            'updated_at_formatted' => $this->updated_at?->format('d/m/Y h:i A'),
        ];
    }
}
