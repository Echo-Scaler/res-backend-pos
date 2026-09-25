<?php

namespace App\Http\Resources;

use App\Models\ExpenseBudget;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ExpenseBudget
 */
class ExpenseBudgetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $budget = (int) $this->budget_amount;

        return [
            'id' => $this->id,
            'restaurant_id' => $this->restaurant_id,
            'category_id' => $this->category_id,
            'category_name' => $this->category?->name,
            'fiscal_year' => (int) $this->fiscal_year,
            'period_type' => $this->period_type,
            'period_month' => $this->period_month,
            'budget_amount' => $budget,
            'formatted_budget_amount' => number_format($budget, 0).' MMK',
            'currency' => 'MMK',
            'alert_threshold_percent' => (int) $this->alert_threshold_percent,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
