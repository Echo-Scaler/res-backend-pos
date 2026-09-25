<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'name',
        'code',
        'contact_person',
        'phone',
        'email',
        'address',
        'tax_id',
        'bank_name',
        'bank_account_number',
        'payment_terms_days',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'payment_terms_days' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'vendor_id');
    }

    public function recurringExpenses(): HasMany
    {
        return $this->hasMany(RecurringExpense::class, 'vendor_id');
    }
}
