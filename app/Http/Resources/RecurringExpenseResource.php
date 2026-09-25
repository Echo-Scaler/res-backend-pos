<?php

namespace App\Http\Resources;

use App\Models\RecurringExpense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin RecurringExpense
 */
class RecurringExpenseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $amount = (int) $this->amount;

        return [
            'id' => $this->id,
            'restaurant_id' => $this->restaurant_id,
            'title' => $this->title,
            'category_id' => $this->category_id,
            'category_name' => $this->category?->name,
            'vendor_id' => $this->vendor_id,
            'vendor_name' => $this->vendor?->name,
            'amount' => $amount,
            'formatted_amount' => number_format($amount, 0).' MMK',
            'currency' => 'MMK',
            'frequency' => $this->frequency,
            'start_date' => $this->start_date ? Carbon::parse($this->start_date)->format('Y-m-d') : null,
            'end_date' => $this->end_date ? Carbon::parse($this->end_date)->format('Y-m-d') : null,
            'next_due_date' => $this->next_due_date ? Carbon::parse($this->next_due_date)->format('Y-m-d') : null,
            'payment_method' => $this->payment_method,
            'auto_submit' => (bool) $this->auto_submit,
            'is_active' => (bool) $this->is_active,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
