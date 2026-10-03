<?php

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Order
 */
class OrderSlipResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'table_number' => $this->table_number,
            'guest_count' => (int) $this->guest_count,
            'order_type' => $this->order_type,
            'status' => $this->status,
            'kitchen_status' => $this->kitchen_status,
            'reprint_count' => (int) $this->reprint_count,
            'is_reprint' => $this->reprint_count > 0,
            'reprint_badge' => $this->reprint_count > 0 ? "REPRINT #{$this->reprint_count}" : null,
            'verified_at' => $this->verified_at?->toIso8601String(),
            'verifier_name' => $this->verifier?->name,
            'subtotal' => (int) $this->subtotal,
            'tax_amount' => (int) $this->tax_amount,
            'total_amount' => (int) $this->total_amount,
            'currency' => 'MMK',
            'formatted_total' => number_format((int) $this->total_amount, 0).' MMK',
            'created_at' => $this->created_at?->toIso8601String(),
            'items' => $this->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'item_name' => $item->item_name,
                    'quantity' => (int) $item->quantity,
                    'special_notes' => $item->special_notes,
                    'unit_price' => (int) $item->unit_price,
                    'formatted_unit_price' => number_format((int) $item->unit_price, 0).' MMK',
                    'subtotal' => (int) $item->subtotal,
                    'formatted_subtotal' => number_format((int) $item->subtotal, 0).' MMK',
                    'is_cooked' => (bool) $item->is_cooked,
                    'is_verified' => (bool) $item->is_verified,
                    'is_voided' => (bool) $item->is_voided,
                ];
            }),
        ];
    }
}
