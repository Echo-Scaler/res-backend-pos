<?php

namespace App\Http\Resources\Pos;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReceiptResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $restaurant = $this->restaurant;
        $payment = $this->payments->last();

        return [
            'order_id' => $this->id,
            'order_number' => $this->order_number,
            'table_number' => $this->table_number,
            'guest_count' => $this->guest_count,
            'status' => $this->status,
            'restaurant' => [
                'name' => $restaurant ? $restaurant->name : 'Restaurant POS',
                'address' => $restaurant->address ?? 'Yangon, Myanmar',
                'phone' => $restaurant->phone ?? '+95 9 12345678',
                'tax_number' => 'CT-MM-2026-9812',
            ],
            'cashier_name' => $this->cashDrawerSession && $this->cashDrawerSession->cashier
                ? $this->cashDrawerSession->cashier->name
                : ($this->staff ? $this->staff->name : 'Cashier'),
            'date_time' => $this->updated_at ? $this->updated_at->format('d-M-Y h:i A') : now()->format('d-M-Y h:i A'),
            'subtotal' => $this->subtotal,
            'formatted_subtotal' => number_format($this->subtotal, 0).' MMK',
            'discount_amount' => $this->discount_amount,
            'formatted_discount' => number_format($this->discount_amount, 0).' MMK',
            'tax_amount' => $this->tax_amount,
            'formatted_tax' => number_format($this->tax_amount, 0).' MMK',
            'service_charge' => $this->service_charge ?? 0,
            'formatted_service_charge' => number_format($this->service_charge ?? 0, 0).' MMK',
            'total_amount' => $this->total_amount,
            'formatted_total' => number_format($this->total_amount, 0).' MMK',
            'paid_amount' => $this->paid_amount,
            'formatted_paid' => number_format($this->paid_amount, 0).' MMK',
            'currency' => 'MMK',
            'currency_symbol' => 'Ks ',
            'payment' => $payment ? [
                'method' => $payment->payment_method,
                'tendered_amount' => $payment->tendered_amount,
                'formatted_tendered' => number_format($payment->tendered_amount, 0).' MMK',
                'change_amount' => $payment->change_amount,
                'formatted_change' => number_format($payment->change_amount, 0).' MMK',
                'reference_no' => $payment->reference_no,
            ] : null,
            'items' => $this->items->map(function ($item) {
                return [
                    'name' => $item->item_name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'formatted_price' => number_format($item->unit_price, 0).' MMK',
                    'subtotal' => $item->subtotal,
                    'formatted_subtotal' => number_format($item->subtotal, 0).' MMK',
                    'special_notes' => $item->special_notes,
                ];
            }),
        ];
    }
}
