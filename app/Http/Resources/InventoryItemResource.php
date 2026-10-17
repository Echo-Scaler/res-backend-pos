<?php

namespace App\Http\Resources;

use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin InventoryItem
 */
class InventoryItemResource extends JsonResource
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
            'restaurant_id' => $this->restaurant_id,
            'name' => $this->name,
            'sku' => $this->sku,
            'unit' => $this->unit,
            'current_stock' => (float) $this->current_stock,
            'min_stock_alert' => (float) $this->min_stock_alert,
            'is_low_stock' => $this->isLowStock(),
            'unit_cost' => (float) $this->unit_cost,
            'unit_cost_formatted' => number_format((float) $this->unit_cost, 0).' MMK',
            'total_valuation' => (float) ($this->current_stock * $this->unit_cost),
            'total_valuation_formatted' => number_format((float) ($this->current_stock * $this->unit_cost), 0).' MMK',
            'currency' => 'MMK',
            'currency_symbol' => 'Ks ',
            'supplier_name' => $this->supplier_name,
            'is_active' => (bool) $this->is_active,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
