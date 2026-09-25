<?php

namespace App\Http\Resources;

use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Expense
 */
class ExpenseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $amount = (int) $this->amount;
        $taxAmount = (int) $this->tax_amount;
        $totalAmount = (int) ($this->total_amount ?: ($amount + $taxAmount));

        return [
            'id' => $this->id,
            'expense_number' => $this->expense_number,
            'restaurant_id' => $this->restaurant_id,
            'category_id' => $this->category_id,
            'category_name' => $this->category_display_name,
            'vendor_id' => $this->vendor_id,
            'vendor_name' => $this->vendor?->name,
            'title' => $this->title,
            'amount' => $amount,
            'formatted_amount' => number_format($amount, 0).' MMK',
            'tax_amount' => $taxAmount,
            'formatted_tax_amount' => number_format($taxAmount, 0).' MMK',
            'total_amount' => $totalAmount,
            'formatted_total_amount' => number_format($totalAmount, 0).' MMK',
            'currency' => 'MMK',
            'currency_symbol' => 'Ks ',
            'expense_date' => $this->expense_date ? Carbon::parse($this->expense_date)->format('Y-m-d') : null,
            'due_date' => $this->due_date ? Carbon::parse($this->due_date)->format('Y-m-d') : null,
            'payment_date' => $this->payment_date ? Carbon::parse($this->payment_date)->format('Y-m-d') : null,
            'is_overdue' => $this->isOverdue(),
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'payment_method' => $this->payment_method,
            'payment_reference' => $this->payment_reference,
            'description' => $this->description,
            'reason' => $this->reason,
            'notes' => $this->notes,
            'rejection_reason' => $this->rejection_reason,
            'void_reason' => $this->void_reason,
            'receipt_url' => $this->receipt_path ? url('storage/'.$this->receipt_path) : null,
            'created_by' => $this->creator ? [
                'id' => $this->creator->id,
                'name' => $this->creator->name,
            ] : null,
            'approved_by' => $this->approver ? [
                'id' => $this->approver->id,
                'name' => $this->approver->name,
                'approved_at' => $this->approved_at?->toIso8601String(),
            ] : null,
            'rejected_by' => $this->rejecter ? [
                'id' => $this->rejecter->id,
                'name' => $this->rejecter->name,
                'rejected_at' => $this->rejected_at?->toIso8601String(),
            ] : null,
            'paid_by' => $this->payer ? [
                'id' => $this->payer->id,
                'name' => $this->payer->name,
                'paid_at' => $this->paid_at?->toIso8601String(),
            ] : null,
            'voided_by' => $this->voider ? [
                'id' => $this->voider->id,
                'name' => $this->voider->name,
                'voided_at' => $this->voided_at?->toIso8601String(),
            ] : null,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
