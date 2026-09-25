<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfitLossResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'revenue' => $this->resource['revenue'] ?? 0,
            'cogs' => $this->resource['cogs'] ?? 0,
            'gross_profit' => $this->resource['gross_profit'] ?? 0,
            'operating_expenses' => $this->resource['operating_expenses'] ?? [],
            'estimated_net_profit' => $this->resource['estimated_net_profit'] ?? 0,
            'net_profit_margin' => $this->resource['net_profit_margin'] ?? 0.0,
            'currency' => $this->resource['currency'] ?? 'MMK',
            'currency_symbol' => $this->resource['currency_symbol'] ?? 'Ks ',
        ];
    }
}
