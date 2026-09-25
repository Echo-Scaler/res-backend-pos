<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VoidAnalysisResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'cancelled_orders_count' => $this->resource['cancelled_orders_count'] ?? 0,
            'voided_items_count' => $this->resource['voided_items_count'] ?? 0,
            'total_cancelled_amount' => $this->resource['total_cancelled_amount'] ?? 0,
            'records' => $this->resource['records'] ?? [],
        ];
    }
}
