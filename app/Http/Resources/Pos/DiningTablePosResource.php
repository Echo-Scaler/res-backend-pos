<?php

namespace App\Http\Resources\Pos;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiningTablePosResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $activeOrder = $this->active_order;

        return [
            'id' => $this->id,
            'table_number' => $this->table_number,
            'name' => $this->name,
            'floor_area' => $this->floor_area,
            'seating_capacity' => $this->seating_capacity,
            'status' => $this->status,
            'qr_token' => $this->qr_token,
            'active_order' => $activeOrder ? [
                'id' => $activeOrder->id,
                'order_number' => $activeOrder->order_number,
                'guest_count' => $activeOrder->guest_count,
                'status' => $activeOrder->status,
                'kitchen_status' => $activeOrder->kitchen_status,
                'items_count' => $activeOrder->items ? $activeOrder->items->count() : 0,
                'subtotal' => $activeOrder->subtotal,
                'formatted_subtotal' => number_format($activeOrder->subtotal, 0).' MMK',
                'total_amount' => $activeOrder->total_amount,
                'formatted_total' => number_format($activeOrder->total_amount, 0).' MMK',
                'elapsed_minutes' => $activeOrder->created_at ? $activeOrder->created_at->diffInMinutes(now()) : 0,
            ] : null,
        ];
    }
}
