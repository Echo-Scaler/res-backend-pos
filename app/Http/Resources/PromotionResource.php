<?php

namespace App\Http\Resources;

use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Promotion
 */
class PromotionResource extends JsonResource
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
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'value' => (float) $this->value,
            'formatted_discount' => $this->type === 'PERCENTAGE'
                ? ((float) $this->value.'% OFF')
                : (number_format((float) $this->value, 0).' MMK OFF'),
            'min_order_amount' => (float) $this->min_order_amount,
            'max_discount_amount' => (float) $this->max_discount_amount,
            'start_date' => $this->start_date instanceof \DateTimeInterface ? $this->start_date->format('Y-m-d') : ($this->start_date ? (string) $this->start_date : null),
            'end_date' => $this->end_date instanceof \DateTimeInterface ? $this->end_date->format('Y-m-d') : ($this->end_date ? (string) $this->end_date : null),
            'usage_limit' => $this->usage_limit,
            'used_count' => $this->used_count,
            'is_active' => (bool) $this->is_active,
            'is_valid_now' => $this->isValidNow(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
