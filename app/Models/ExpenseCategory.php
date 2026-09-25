<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'name',
        'code',
        'description',
        'gl_account_code',
        'color',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'category_id');
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(ExpenseBudget::class, 'category_id');
    }

    public function recurringExpenses(): HasMany
    {
        return $this->hasMany(RecurringExpense::class, 'category_id');
    }
}
