<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    public const CATEGORY_FINANCIAL = 'FINANCIAL';

    public const CATEGORY_ORDER_VOID = 'ORDER_VOID';

    public const CATEGORY_MENU_PRICE = 'MENU_PRICE';

    public const CATEGORY_ACCESS_SECURITY = 'ACCESS_SECURITY';

    public const CATEGORY_GENERAL = 'GENERAL';

    public const SEVERITY_INFO = 'INFO';

    public const SEVERITY_WARNING = 'WARNING';

    public const SEVERITY_CRITICAL = 'CRITICAL';

    protected $fillable = [
        'restaurant_id',
        'user_id',
        'user_name',
        'role_name',
        'action',
        'category',
        'severity',
        'resource_type',
        'resource_id',
        'old_value',
        'new_value',
        'metadata',
        'ip_address',
        'user_agent',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for restaurant tenant filtering.
     */
    public function scopeForRestaurant(Builder $query, int $restaurantId): Builder
    {
        return $query->where('restaurant_id', $restaurantId);
    }

    /**
     * Scope to apply advanced multi-criteria filters.
     *
     * @param  array<string, mixed>  $filters
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when(! empty($filters['category']), function (Builder $q) use ($filters) {
                $q->where('category', $filters['category']);
            })
            ->when(! empty($filters['severity']), function (Builder $q) use ($filters) {
                $q->where('severity', $filters['severity']);
            })
            ->when(! empty($filters['user_id']), function (Builder $q) use ($filters) {
                $q->where('user_id', $filters['user_id']);
            })
            ->when(! empty($filters['action']), function (Builder $q) use ($filters) {
                $q->where('action', $filters['action']);
            })
            ->when(! empty($filters['date_from']), function (Builder $q) use ($filters) {
                $q->whereDate('created_at', '>=', $filters['date_from']);
            })
            ->when(! empty($filters['date_to']), function (Builder $q) use ($filters) {
                $q->whereDate('created_at', '<=', $filters['date_to']);
            })
            ->when(! empty($filters['search']), function (Builder $q) use ($filters) {
                $term = '%'.$filters['search'].'%';
                $q->where(function (Builder $sub) use ($term) {
                    $sub->where('action', 'LIKE', $term)
                        ->orWhere('user_name', 'LIKE', $term)
                        ->orWhere('resource_type', 'LIKE', $term)
                        ->orWhere('old_value', 'LIKE', $term)
                        ->orWhere('new_value', 'LIKE', $term)
                        ->orWhere('ip_address', 'LIKE', $term);
                });
            });
    }

    /**
     * Resolve badge styling for UI display.
     */
    public function getSeverityColorAttribute(): string
    {
        return match (strtoupper((string) $this->severity)) {
            self::SEVERITY_CRITICAL => 'danger',
            self::SEVERITY_WARNING => 'warning',
            default => 'info',
        };
    }
}
