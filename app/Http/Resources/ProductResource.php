<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Product
 */
class ProductResource extends JsonResource
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
            'category_id' => $this->category_id,
            'category_name' => $this->category?->name,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'price' => (float) $this->price,
            'formatted_price' => number_format((float) $this->price, 0).' MMK',
            'cost_price' => (float) $this->cost_price,
            'image_url' => $this->image_url,
            'is_available' => (bool) $this->is_available,
            'preparation_time' => $this->preparation_time,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
