<?php

namespace App\Models;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class DiningTable extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'table_number',
        'name',
        'seating_capacity',
        'floor_area',
        'status',
        'qr_token',
        'current_order_id',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'seating_capacity' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (DiningTable $table) {
            if (empty($table->qr_token)) {
                $table->qr_token = Str::random(32);
            }
        });
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function currentOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'current_order_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'table_number', 'table_number');
    }

    /**
     * Get active order currently seated at this table.
     */
    public function getActiveOrderAttribute(): ?Order
    {
        if ($this->relationLoaded('currentOrder') && $this->currentOrder) {
            return $this->currentOrder;
        }

        if ($this->current_order_id && $this->currentOrder) {
            return $this->currentOrder;
        }

        if ($this->relationLoaded('orders')) {
            return $this->orders->first(function ($order) {
                return ! in_array($order->status, ['COMPLETED', 'CANCELLED', 'VOID']);
            });
        }

        return $this->orders()
            ->whereNotIn('status', ['COMPLETED', 'CANCELLED', 'VOID'])
            ->latest('id')
            ->first();
    }

    public function getOrderUrl(): string
    {
        return url('/order/table/'.$this->qr_token);
    }

    public function getQrCodeSvg(): string
    {
        $options = new QROptions([
            'outputBase64' => false,
        ]);

        return (new QRCode($options))->render($this->getOrderUrl());
    }

    public function getQrCodeDataUri(): string
    {
        return (new QRCode)->render($this->getOrderUrl());
    }

    public function regenerateQrToken(): string
    {
        $this->qr_token = Str::random(32);
        $this->save();

        return $this->qr_token;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeArea(Builder $query, ?string $area): Builder
    {
        return $area ? $query->where('floor_area', $area) : $query;
    }

    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        return $status ? $query->where('status', $status) : $query;
    }
}
