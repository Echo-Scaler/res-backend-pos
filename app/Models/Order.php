<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'order_number',
        'customer_id',
        'customer_name',
        'table_number',
        'guest_count',
        'order_type',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'cogs_amount',
        'total_amount',
        'paid_amount',
        'refund_amount',
        'outstanding_amount',
        'status',
        'payment_status',
        'cancellation_reason',
        'cancelled_at',
        'staff_id',
        'approved_by',
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'integer',
            'discount_amount' => 'integer',
            'tax_amount' => 'integer',
            'cogs_amount' => 'integer',
            'total_amount' => 'integer',
            'paid_amount' => 'integer',
            'refund_amount' => 'integer',
            'outstanding_amount' => 'integer',
            'guest_count' => 'integer',
            'cancelled_at' => 'datetime',
        ];
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
