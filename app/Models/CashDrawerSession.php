<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashDrawerSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'user_id',
        'terminal_code',
        'opened_at',
        'closed_at',
        'opening_float',
        'cash_sales',
        'digital_sales',
        'cash_in',
        'cash_out',
        'expected_cash',
        'closing_actual_cash',
        'cash_difference',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'opening_float' => 'integer',
            'cash_sales' => 'integer',
            'digital_sales' => 'integer',
            'cash_in' => 'integer',
            'cash_out' => 'integer',
            'expected_cash' => 'integer',
            'closing_actual_cash' => 'integer',
            'cash_difference' => 'integer',
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(CashDrawerTransaction::class, 'cash_drawer_session_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'cash_drawer_session_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'cash_drawer_session_id');
    }

    /**
     * Recalculate expected cash and sales totals for this session.
     */
    public function recalculate(): void
    {
        $cashSales = (int) $this->payments()->where('payment_method', 'CASH')->where('status', 'SUCCESS')->sum('amount');
        $digitalSales = (int) $this->payments()->where('payment_method', '!=', 'CASH')->where('status', 'SUCCESS')->sum('amount');
        $cashIn = (int) $this->transactions()->where('type', 'CASH_IN')->sum('amount');
        $cashOut = (int) $this->transactions()->where('type', 'CASH_OUT')->sum('amount');

        $expected = $this->opening_float + $cashSales + $cashIn - $cashOut;

        $this->update([
            'cash_sales' => $cashSales,
            'digital_sales' => $digitalSales,
            'cash_in' => $cashIn,
            'cash_out' => $cashOut,
            'expected_cash' => $expected,
        ]);
    }
}
