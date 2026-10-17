<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaxServiceSettingResource extends JsonResource
{
    /**
     * Transform the tax and service charge configuration into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'tax' => [
                'is_enabled' => (bool) $this->is_tax_enabled,
                'name' => $this->tax_name ?: 'Commercial Tax',
                'rate_percent' => (float) $this->default_tax_percent,
                'calculation_mode' => $this->tax_calculation_mode ?: 'inclusive',
                'tin_id' => $this->tax_id,
                'office_name' => $this->tax_office_name,
                'channels' => [
                    'dine_in' => (bool) $this->apply_tax_dine_in,
                    'takeaway' => (bool) $this->apply_tax_takeaway,
                    'delivery' => (bool) $this->apply_tax_delivery,
                    'qr_order' => (bool) $this->apply_tax_qr_order,
                ],
            ],
            'service_charge' => [
                'is_enabled' => (bool) $this->is_service_charge_enabled,
                'name' => $this->service_charge_name ?: 'Service Charge',
                'rate_percent' => (float) $this->default_service_charge_percent,
                'is_taxable' => (bool) $this->is_service_charge_taxable,
                'dine_in_only' => (bool) $this->service_charge_dine_in_only,
                'takeaway_packaging_fee' => (int) $this->takeaway_packaging_fee,
                'formatted_packaging_fee' => number_format((int) $this->takeaway_packaging_fee, 0).' MMK',
            ],
            'rounding' => [
                'method' => $this->rounding_method ?: 'round_50',
                'direction' => $this->rounding_direction ?: 'nearest',
                'round_cash_only' => (bool) $this->round_cash_only,
            ],
            'receipt' => [
                'show_tax_breakdown' => (bool) $this->show_tax_breakdown_on_receipt,
                'show_service_charge' => (bool) $this->show_service_charge_on_receipt,
                'print_tax_stamp_seal' => (bool) $this->print_tax_stamp_seal,
                'disclaimer_text' => $this->tax_disclaimer_text,
            ],
            'currency' => 'MMK',
            'currency_symbol' => 'Ks ',
            'updated_at_formatted' => $this->updated_at?->format('d/m/Y h:i A'),
        ];
    }
}
