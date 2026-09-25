<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportAnalyticsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'period_label' => $this->resource['period_label'] ?? '',
            'kpis' => $this->resource['kpis'] ?? [],
            'today_overview' => $this->resource['today_overview'] ?? [],
            'daily_breakdown' => $this->resource['daily_breakdown'] ?? [],
            'sales_by_category' => $this->resource['sales_by_category'] ?? [],
            'sales_trends' => $this->resource['sales_trends'] ?? [],
            'order_types' => $this->resource['order_types'] ?? [],
            'sales_comparison' => $this->resource['sales_comparison'] ?? [],
        ];
    }
}
