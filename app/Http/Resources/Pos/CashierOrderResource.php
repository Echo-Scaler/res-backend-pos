<?php

namespace App\Http\Resources\Pos;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CashierOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'table_number' => $this->table_number,
            'guest_count' => $this->guest_count,
            'status' => $this->status,
            'kitchen_status' => $this->kitchen_status,
            'payment_status' => $this->payment_status,
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
            'outstanding_amount' => max(0, $this->total_amount - $this->paid_amount),
            'formatted_outstanding' => number_format(max(0, $this->total_amount - $this->paid_amount), 0).' MMK',
            'currency' => 'MMK',
            'currency_symbol' => 'Ks ',
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'elapsed_minutes' => $this->created_at ? $this->created_at->diffInMinutes(now()) : 0,
            'items' => $this->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'item_name' => $item->item_name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'formatted_price' => number_format($item->unit_price, 0).' MMK',
                    'subtotal' => $item->subtotal,
                    'formatted_subtotal' => number_format($item->subtotal, 0).' MMK',
                    'special_notes' => $item->special_notes,
                    'is_cooked' => (bool) $item->is_cooked,
                    'is_verified' => (bool) $item->is_verified,
                ];
            }),
        ];
    }
}
