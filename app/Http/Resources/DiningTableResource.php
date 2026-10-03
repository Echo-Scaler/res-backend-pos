<?php

namespace App\Http\Resources;

use App\Models\DiningTable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin DiningTable
 */
class DiningTableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $activeOrder = $this->active_order;

        return [
            'id' => $this->id,
            'restaurant_id' => $this->restaurant_id,
            'table_number' => $this->table_number,
            'name' => $this->name,
            'seating_capacity' => (int) $this->seating_capacity,
            'floor_area' => $this->floor_area,
            'status' => $this->status,
            'qr_token' => $this->qr_token,
            'order_url' => $this->getOrderUrl(),
            'current_order_id' => $this->current_order_id,
            'active_order' => $activeOrder ? [
                'id' => $activeOrder->id,
                'order_number' => $activeOrder->order_number,
                'kitchen_status' => $activeOrder->kitchen_status,
                'reprint_count' => (int) $activeOrder->reprint_count,
                'total_amount' => (int) $activeOrder->total_amount,
                'currency' => 'MMK',
                'formatted_total' => number_format($activeOrder->total_amount, 0).' MMK',
                'items_count' => $activeOrder->items->count(),
                'items' => $activeOrder->items->map(fn ($item) => [
                    'id' => $item->id,
                    'item_name' => $item->item_name,
                    'quantity' => (int) $item->quantity,
                    'special_notes' => $item->special_notes,
                    'subtotal' => (int) $item->subtotal,
                    'currency' => 'MMK',
                    'formatted_subtotal' => number_format($item->subtotal, 0).' MMK',
                ]),
            ] : null,
            'is_active' => (bool) $this->is_active,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
