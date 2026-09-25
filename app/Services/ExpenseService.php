<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Expense;
use App\Models\ExpenseAttachment;
use App\Models\ExpenseTransaction;
use App\Models\RecurringExpense;
use App\Models\Restaurant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ExpenseService
{
    /**
     * Create a new expense entry.
     *
     * @param  array<string, mixed>  $data
     */
    public function createExpense(array $data, User $user, ?UploadedFile $receipt = null): Expense
    {
        return DB::transaction(function () use ($data, $user, $receipt) {
            $restaurantId = (int) ($data['restaurant_id'] ?? $user->restaurant_id);
            $amount = (int) ($data['amount'] ?? 0);
            $taxAmount = (int) ($data['tax_amount'] ?? 0);
            $totalAmount = $amount + $taxAmount;

            $status = ! empty($data['submit_now'])
                ? Expense::STATUS_PENDING_APPROVAL
                : ($data['status'] ?? Expense::STATUS_DRAFT);

            $expenseNumber = $data['expense_number'] ?? $this->generateExpenseNumber($restaurantId);

            $expense = Expense::create([
                'restaurant_id' => $restaurantId,
                'expense_number' => $expenseNumber,
                'category_id' => $data['category_id'] ?? null,
                'category' => $data['category'] ?? null,
                'vendor_id' => $data['vendor_id'] ?? null,
                'title' => $data['title'],
                'amount' => $amount,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'expense_date' => $data['expense_date'] ?? now()->toDateString(),
                'due_date' => $data['due_date'] ?? null,
                'payment_method' => $data['payment_method'] ?? null,
                'status' => $status,
                'payment_status' => Expense::PAYMENT_STATUS_UNPAID,
                'description' => $data['description'] ?? null,
                'reason' => $data['reason'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => $user->id,
                'recurring_expense_id' => $data['recurring_expense_id'] ?? null,
            ]);

            if ($receipt) {
                $this->attachReceipt($expense, $receipt, $user);
            }

            // Record Audit Log
            AuditLog::create([
                'restaurant_id' => $restaurantId,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'action' => 'EXPENSE_CREATED',
                'resource_type' => 'Expense (#'.$expense->expense_number.')',
                'resource_id' => $expense->id,
                'old_value' => null,
                'new_value' => json_encode([
                    'title' => $expense->title,
                    'amount' => $expense->amount,
                    'total_amount' => $expense->total_amount,
                    'status' => $expense->status,
                    'created_by' => $user->name,
                ]),
                'ip_address' => request()->ip() ?? '127.0.0.1',
            ]);

            return $expense;
        });
    }

    /**
     * Update an existing expense.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateExpense(Expense $expense, array $data, User $user, ?UploadedFile $receipt = null): Expense
    {
        if ($expense->isPaid()) {
            throw new InvalidArgumentException('Paid expenses cannot be directly modified. Void and recreate if needed.');
        }

        if ($expense->isVoid()) {
            throw new InvalidArgumentException('Voided expenses cannot be updated.');
        }

        return DB::transaction(function () use ($expense, $data, $user, $receipt) {
            $oldValues = $expense->only(['title', 'amount', 'tax_amount', 'category_id', 'vendor_id', 'status', 'due_date']);

            $amount = isset($data['amount']) ? (int) $data['amount'] : $expense->amount;
            $taxAmount = isset($data['tax_amount']) ? (int) $data['tax_amount'] : $expense->tax_amount;
            $totalAmount = $amount + $taxAmount;

            $updatePayload = [
                'title' => $data['title'] ?? $expense->title,
                'category_id' => array_key_exists('category_id', $data) ? $data['category_id'] : $expense->category_id,
                'vendor_id' => array_key_exists('vendor_id', $data) ? $data['vendor_id'] : $expense->vendor_id,
                'amount' => $amount,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'expense_date' => $data['expense_date'] ?? $expense->expense_date,
                'due_date' => array_key_exists('due_date', $data) ? $data['due_date'] : $expense->due_date,
                'payment_method' => $data['payment_method'] ?? $expense->payment_method,
                'description' => array_key_exists('description', $data) ? $data['description'] : $expense->description,
                'reason' => array_key_exists('reason', $data) ? $data['reason'] : $expense->reason,
                'notes' => array_key_exists('notes', $data) ? $data['notes'] : $expense->notes,
            ];

            if (! empty($data['submit_now']) && in_array($expense->status, [Expense::STATUS_DRAFT, Expense::STATUS_REJECTED], true)) {
                $updatePayload['status'] = Expense::STATUS_PENDING_APPROVAL;
                $updatePayload['rejection_reason'] = null;
                $updatePayload['rejected_by'] = null;
                $updatePayload['rejected_at'] = null;
            }

            $expense->update($updatePayload);

            if ($receipt) {
                $this->attachReceipt($expense, $receipt, $user);
            }

            AuditLog::create([
                'restaurant_id' => $expense->restaurant_id,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'action' => 'EXPENSE_UPDATED',
                'resource_type' => 'Expense (#'.$expense->expense_number.')',
                'resource_id' => $expense->id,
                'old_value' => json_encode($oldValues),
                'new_value' => json_encode($expense->only(['title', 'amount', 'tax_amount', 'category_id', 'vendor_id', 'status', 'due_date'])),
                'ip_address' => request()->ip() ?? '127.0.0.1',
            ]);

            return $expense->fresh();
        });
    }

    /**
     * Submit a draft or rejected expense for approval.
     */
    public function submitForApproval(Expense $expense, User $user): Expense
    {
        if (! in_array($expense->status, [Expense::STATUS_DRAFT, Expense::STATUS_REJECTED], true)) {
            throw new InvalidArgumentException('Only draft or rejected expenses can be submitted for approval.');
        }

        $expense->update([
            'status' => Expense::STATUS_PENDING_APPROVAL,
            'rejection_reason' => null,
            'rejected_by' => null,
            'rejected_at' => null,
        ]);

        AuditLog::create([
            'restaurant_id' => $expense->restaurant_id,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'action' => 'EXPENSE_SUBMITTED',
            'resource_type' => 'Expense (#'.$expense->expense_number.')',
            'resource_id' => $expense->id,
            'old_value' => 'Status: '.$expense->getOriginal('status'),
            'new_value' => 'Status: PENDING_APPROVAL',
            'ip_address' => request()->ip() ?? '127.0.0.1',
        ]);

        return $expense;
    }

    /**
     * Approve an expense.
     */
    public function approveExpense(Expense $expense, User $user, ?string $notes = null): Expense
    {
        if ($expense->status !== Expense::STATUS_PENDING_APPROVAL) {
            throw new InvalidArgumentException('Only expenses with status PENDING_APPROVAL can be approved.');
        }

        $expense->update([
            'status' => Expense::STATUS_APPROVED,
            'approved_by' => $user->id,
            'approved_at' => now(),
            'notes' => $notes ? trim(($expense->notes ?? '')."\n[Approval Note]: ".$notes) : $expense->notes,
        ]);

        AuditLog::create([
            'restaurant_id' => $expense->restaurant_id,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'action' => 'EXPENSE_APPROVED',
            'resource_type' => 'Expense (#'.$expense->expense_number.')',
            'resource_id' => $expense->id,
            'old_value' => 'Status: PENDING_APPROVAL',
            'new_value' => 'Status: APPROVED by '.$user->name.($notes ? ' ('.$notes.')' : ''),
            'ip_address' => request()->ip() ?? '127.0.0.1',
        ]);

        return $expense;
    }

    /**
     * Reject an expense.
     */
    public function rejectExpense(Expense $expense, User $user, string $reason): Expense
    {
        if ($expense->status !== Expense::STATUS_PENDING_APPROVAL) {
            throw new InvalidArgumentException('Only expenses with status PENDING_APPROVAL can be rejected.');
        }

        $expense->update([
            'status' => Expense::STATUS_REJECTED,
            'rejected_by' => $user->id,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);

        AuditLog::create([
            'restaurant_id' => $expense->restaurant_id,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'action' => 'EXPENSE_REJECTED',
            'resource_type' => 'Expense (#'.$expense->expense_number.')',
            'resource_id' => $expense->id,
            'old_value' => 'Status: PENDING_APPROVAL',
            'new_value' => 'Status: REJECTED by '.$user->name.' (Reason: '.$reason.')',
            'ip_address' => request()->ip() ?? '127.0.0.1',
        ]);

        return $expense;
    }

    /**
     * Process payment for an approved expense (Cash Drawer / Bank Ledger Integration).
     *
     * @param  array<string, mixed>  $paymentData
     */
    public function processPayment(Expense $expense, array $paymentData, User $user): ExpenseTransaction
    {
        if (! in_array($expense->status, [Expense::STATUS_APPROVED, Expense::STATUS_PAYMENT_PENDING], true)) {
            throw new InvalidArgumentException('Only approved expenses can be processed for payment.');
        }

        if ($expense->isPaid()) {
            throw new InvalidArgumentException('This expense has already been marked as paid.');
        }

        return DB::transaction(function () use ($expense, $paymentData, $user) {
            $paymentMethod = strtoupper($paymentData['payment_method'] ?? 'CASH');
            $paymentDate = $paymentData['payment_date'] ?? now()->toDateString();
            $paymentRef = $paymentData['payment_reference'] ?? ('REF-'.Str::upper(Str::random(8)));
            $amountToPay = (int) ($paymentData['amount'] ?? ($expense->total_amount ?: $expense->amount));

            // Determine transaction type and account
            $transactionType = 'BANK_ACCOUNT';
            $defaultAccount = 'Corporate Bank Account';

            if ($paymentMethod === 'CASH') {
                $transactionType = 'CASH_DRAWER';
                $defaultAccount = 'Main POS Cash Register Drawer #1';
            } elseif (in_array($paymentMethod, ['KBZPAY', 'WAVEPAY', 'AYA_PAY'], true)) {
                $transactionType = 'DIGITAL_WALLET';
                $defaultAccount = $paymentMethod.' Merchant Account';
            }

            $accountName = $paymentData['account_or_drawer_name'] ?? $defaultAccount;

            // Update Expense
            $expense->update([
                'status' => Expense::STATUS_PAID,
                'payment_status' => Expense::PAYMENT_STATUS_PAID,
                'payment_method' => $paymentMethod,
                'payment_date' => $paymentDate,
                'payment_reference' => $paymentRef,
                'paid_by' => $user->id,
                'paid_at' => now(),
            ]);

            // Create Expense Transaction
            $transaction = ExpenseTransaction::create([
                'restaurant_id' => $expense->restaurant_id,
                'expense_id' => $expense->id,
                'transaction_type' => $transactionType,
                'payment_method' => $paymentMethod,
                'amount' => $amountToPay,
                'reference_no' => $paymentRef,
                'account_or_drawer_name' => $accountName,
                'status' => 'SUCCESS',
                'notes' => $paymentData['notes'] ?? ('Disbursement for '.$expense->title),
                'processed_by' => $user->id,
            ]);

            AuditLog::create([
                'restaurant_id' => $expense->restaurant_id,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'action' => 'EXPENSE_PAID',
                'resource_type' => 'Expense (#'.$expense->expense_number.')',
                'resource_id' => $expense->id,
                'old_value' => 'Status: APPROVED, Payment: UNPAID',
                'new_value' => 'Status: PAID via '.$paymentMethod.' ('.$amountToPay.' MMK, Ref: '.$paymentRef.') into '.$accountName,
                'ip_address' => request()->ip() ?? '127.0.0.1',
            ]);

            return $transaction;
        });
    }

    /**
     * Void an expense with strict audit history.
     */
    public function voidExpense(Expense $expense, User $user, string $reason): Expense
    {
        if ($expense->isVoid()) {
            throw new InvalidArgumentException('This expense has already been voided.');
        }

        return DB::transaction(function () use ($expense, $user, $reason) {
            $wasPaid = $expense->isPaid();

            // Reverse any linked transactions
            if ($wasPaid) {
                ExpenseTransaction::where('expense_id', $expense->id)->update([
                    'status' => 'REVERSED',
                    'notes' => DB::raw("CONCAT(COALESCE(notes, ''), ' [REVERSED on void: ".addslashes($reason)."]')"),
                ]);
            }

            $oldStatus = $expense->status;
            $oldPaymentStatus = $expense->payment_status;

            $expense->update([
                'status' => Expense::STATUS_VOID,
                'payment_status' => Expense::PAYMENT_STATUS_VOID,
                'void_reason' => $reason,
                'voided_by' => $user->id,
                'voided_at' => now(),
            ]);

            AuditLog::create([
                'restaurant_id' => $expense->restaurant_id,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'action' => 'EXPENSE_VOIDED',
                'resource_type' => 'Expense (#'.$expense->expense_number.')',
                'resource_id' => $expense->id,
                'old_value' => 'Status: '.$oldStatus.', Payment: '.$oldPaymentStatus,
                'new_value' => 'Status: VOID by '.$user->name.' (Reason: '.$reason.')'.($wasPaid ? ' [Transactions Reversed]' : ''),
                'ip_address' => request()->ip() ?? '127.0.0.1',
            ]);

            return $expense->fresh();
        });
    }

    /**
     * Generate recurring expenses whose next_due_date <= today.
     */
    public function generateRecurringExpenses(Restaurant $restaurant): int
    {
        $today = now()->toDateString();
        $templates = RecurringExpense::where('restaurant_id', $restaurant->id)
            ->where('is_active', true)
            ->whereDate('next_due_date', '<=', $today)
            ->get();

        $generatedCount = 0;

        foreach ($templates as $template) {
            DB::transaction(function () use ($template, $restaurant, &$generatedCount) {
                $status = $template->auto_submit ? Expense::STATUS_PENDING_APPROVAL : Expense::STATUS_DRAFT;
                $expenseNumber = $this->generateExpenseNumber($restaurant->id);

                Expense::create([
                    'restaurant_id' => $restaurant->id,
                    'expense_number' => $expenseNumber,
                    'category_id' => $template->category_id,
                    'vendor_id' => $template->vendor_id,
                    'title' => $template->title.' ('.Carbon::parse($template->next_due_date)->format('M Y').')',
                    'amount' => $template->amount,
                    'tax_amount' => 0,
                    'total_amount' => $template->amount,
                    'expense_date' => $template->next_due_date,
                    'due_date' => $template->next_due_date,
                    'payment_method' => $template->payment_method,
                    'status' => $status,
                    'payment_status' => Expense::PAYMENT_STATUS_UNPAID,
                    'notes' => 'Generated automatically from recurring schedule #'.$template->id,
                    'recurring_expense_id' => $template->id,
                    'created_by' => $template->created_by,
                ]);

                // Calculate next due date
                $currentDue = Carbon::parse($template->next_due_date);
                $nextDue = match ($template->frequency) {
                    'DAILY' => $currentDue->addDay(),
                    'WEEKLY' => $currentDue->addWeek(),
                    'MONTHLY' => $currentDue->addMonth(),
                    'QUARTERLY' => $currentDue->addMonths(3),
                    'YEARLY' => $currentDue->addYear(),
                    default => $currentDue->addMonth(),
                };

                // Check if past end_date
                $isActive = true;
                if ($template->end_date && $nextDue->gt(Carbon::parse($template->end_date))) {
                    $isActive = false;
                }

                $template->update([
                    'last_generated_at' => now(),
                    'next_due_date' => $nextDue->toDateString(),
                    'is_active' => $isActive,
                ]);

                $generatedCount++;
            });
        }

        return $generatedCount;
    }

    /**
     * Attach a receipt file to an expense.
     */
    protected function attachReceipt(Expense $expense, UploadedFile $receipt, User $user): void
    {
        $path = $receipt->store('expenses/'.$expense->restaurant_id, 'public');

        $expense->update(['receipt_path' => $path]);

        ExpenseAttachment::create([
            'expense_id' => $expense->id,
            'file_path' => $path,
            'file_name' => $receipt->getClientOriginalName(),
            'file_size' => $receipt->getSize(),
            'mime_type' => $receipt->getMimeType(),
            'uploaded_by' => $user->id,
        ]);
    }

    /**
     * Generate unique expense number.
     */
    protected function generateExpenseNumber(int $restaurantId): string
    {
        $datePrefix = now()->format('Ym');
        $count = Expense::where('restaurant_id', $restaurantId)
            ->whereDate('created_at', now()->toDateString())
            ->count() + 1;

        return sprintf('EXP-%s-%04d', $datePrefix, $count);
    }
}
