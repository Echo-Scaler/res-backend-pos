<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Expense extends Model
{
    use HasFactory;

    // Status Constants
    public const STATUS_DRAFT = 'DRAFT';

    public const STATUS_PENDING_APPROVAL = 'PENDING_APPROVAL';

    public const STATUS_APPROVED = 'APPROVED';

    public const STATUS_PAYMENT_PENDING = 'PAYMENT_PENDING';

    public const STATUS_PAID = 'PAID';

    public const STATUS_REJECTED = 'REJECTED';

    public const STATUS_VOID = 'VOID';

    // Payment Status Constants
    public const PAYMENT_STATUS_UNPAID = 'UNPAID';

    public const PAYMENT_STATUS_PAID = 'PAID';

    public const PAYMENT_STATUS_VOID = 'VOID';

    protected $fillable = [
        'restaurant_id',
        'expense_number',
        'category_id',
        'category',
        'vendor_id',
        'title',
        'amount',
        'tax_amount',
        'total_amount',
        'expense_date',
        'due_date',
        'payment_date',
        'payment_method',
        'payment_reference',
        'status',
        'payment_status',
        'description',
        'reason',
        'notes',
        'rejection_reason',
        'void_reason',
        'created_by',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'paid_by',
        'paid_at',
        'voided_by',
        'voided_at',
        'receipt_path',
        'recurring_expense_id',
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'tax_amount' => 'integer',
            'total_amount' => 'integer',
            'expense_date' => 'date',
            'due_date' => 'date',
            'payment_date' => 'date',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'paid_at' => 'datetime',
            'voided_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Expense $expense) {
            if (empty($expense->category)) {
                $expense->category = $expense->categoryRelation?->name ?? 'GENERAL';
            }
            if (empty($expense->total_amount)) {
                $expense->total_amount = ((int) $expense->amount) + ((int) ($expense->tax_amount ?? 0));
            }
        });
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function categoryRelation(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejecter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function voider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'voided_by');
    }

    public function recurringTemplate(): BelongsTo
    {
        return $this->belongsTo(RecurringExpense::class, 'recurring_expense_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ExpenseAttachment::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(ExpenseTransaction::class);
    }

    // Status helper methods
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isPendingApproval(): bool
    {
        return $this->status === self::STATUS_PENDING_APPROVAL;
    }

    public function isApproved(): bool
    {
        return in_array($this->status, [self::STATUS_APPROVED, self::STATUS_PAYMENT_PENDING], true);
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID || $this->payment_status === self::PAYMENT_STATUS_PAID;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isVoid(): bool
    {
        return $this->status === self::STATUS_VOID || $this->payment_status === self::PAYMENT_STATUS_VOID;
    }

    public function isOverdue(): bool
    {
        if ($this->isPaid() || $this->isVoid()) {
            return false;
        }

        if (! $this->due_date) {
            return false;
        }

        $dueDate = $this->due_date instanceof CarbonInterface
            ? $this->due_date
            : Carbon::parse($this->due_date);

        return $dueDate->isPast();
    }

    // Query Scopes
    public function scopeForRestaurant(Builder $query, int $restaurantId): Builder
    {
        return $query->where('restaurant_id', $restaurantId);
    }

    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->whereNotIn('status', [self::STATUS_PAID, self::STATUS_VOID])
            ->whereNotNull('due_date')
            ->where('due_date', '<', now()->toDateString());
    }

    // Category display helper (fallback to string if relation not set)
    public function getCategoryDisplayNameAttribute(): string
    {
        if ($this->categoryRelation) {
            return $this->categoryRelation->name;
        }

        return $this->category ?? 'General Expense';
    }

    // Formatted MMK amount according to Rule #6
    public function getFormattedTotalAmountAttribute(): string
    {
        return number_format((int) ($this->total_amount ?: $this->amount), 0).' MMK';
    }
}
