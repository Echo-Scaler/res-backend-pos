<?php

namespace App\Http\Resources;

use App\Models\ExpenseTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ExpenseTransaction
 */
class ExpenseTransactionResource extends JsonResource
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
            'expense_id' => $this->expense_id,
            'transaction_type' => $this->transaction_type,
            'payment_method' => $this->payment_method,
            'amount' => $amount,
            'formatted_amount' => number_format($amount, 0).' MMK',
            'currency' => 'MMK',
            'reference_no' => $this->reference_no,
            'account_or_drawer_name' => $this->account_or_drawer_name,
            'status' => $this->status,
            'notes' => $this->notes,
            'processed_by' => $this->processor ? [
                'id' => $this->processor->id,
                'name' => $this->processor->name,
            ] : null,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
