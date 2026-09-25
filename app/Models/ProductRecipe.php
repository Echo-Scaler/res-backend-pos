<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductRecipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'ingredient_name',
        'unit',
        'quantity_used',
        'cost_amount',
    ];

    protected function casts(): array
    {
        return [
            'quantity_used' => 'float',
            'cost_amount' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
