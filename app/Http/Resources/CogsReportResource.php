<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CogsReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'summary' => $this->resource['summary'] ?? [],
            'featured_recipe' => $this->resource['featured_recipe'] ?? [],
            'other_recipes' => $this->resource['other_recipes'] ?? [],
        ];
    }
}
