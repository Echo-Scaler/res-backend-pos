<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'category_id',
        'item_name',
        'is_alcohol',
        'quantity',
        'unit_price',
        'cost_price',
        'subtotal',
        'profit',
        'is_voided',
        'void_reason',
        'voided_by',
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'is_alcohol' => 'boolean',
            'is_voided' => 'boolean',
            'quantity' => 'integer',
            'unit_price' => 'integer',
            'cost_price' => 'integer',
            'subtotal' => 'integer',
            'profit' => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function voider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'voided_by');
    }
}
