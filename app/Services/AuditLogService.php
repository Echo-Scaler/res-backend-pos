<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogService
{
    /**
     * Record an immutable audit log entry.
     *
     * @param  string  $action  e.g. ORDER_VOID, PRICE_CHANGE, SHIFT_OPEN
     * @param  string  $category  FINANCIAL, ORDER_VOID, MENU_PRICE, ACCESS_SECURITY, GENERAL
     * @param  string  $severity  INFO, WARNING, CRITICAL
     * @param  Model|null  $resource  The affected Eloquent model
     * @param  mixed  $oldValue  Previous state (array or scalar)
     * @param  mixed  $newValue  New state (array or scalar)
     * @param  array<string, mixed>|null  $metadata  Extra contextual info (PIN verified, reasons, terminal ID, etc.)
     * @param  User|null  $user  Override user if performed on behalf or queued
     * @param  int|null  $restaurantId  Override restaurant ID if not resolved via user
     */
    public static function log(
        string $action,
        string $category = AuditLog::CATEGORY_GENERAL,
        string $severity = AuditLog::SEVERITY_INFO,
        ?Model $resource = null,
        mixed $oldValue = null,
        mixed $newValue = null,
        ?array $metadata = null,
        ?User $user = null,
        ?int $restaurantId = null
    ): AuditLog {
        /** @var User|null $currentUser */
        $currentUser = $user ?: Auth::user();

        $resolvedRestaurantId = $restaurantId
            ?: ($currentUser?->restaurant_id ?: ($resource?->getAttribute('restaurant_id') ?? 1));

        $roleName = $currentUser
            ? ($currentUser->roles->first()?->name ?? 'STAFF')
            : 'SYSTEM';

        $formatValue = static function (mixed $val): ?string {
            if ($val === null) {
                return null;
            }
            if (is_array($val) || is_object($val)) {
                return json_encode($val, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }

            return (string) $val;
        };

        $resourceType = null;
        $resourceId = null;
        if ($resource instanceof Model) {
            $resourceType = class_basename($resource);
            $resourceId = $resource->getKey();
        }

        return AuditLog::create([
            'restaurant_id' => $resolvedRestaurantId,
            'user_id' => $currentUser?->id,
            'user_name' => $currentUser?->name ?? 'System Automated',
            'role_name' => $roleName,
            'action' => strtoupper($action),
            'category' => strtoupper($category),
            'severity' => strtoupper($severity),
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'old_value' => $formatValue($oldValue),
            'new_value' => $formatValue($newValue),
            'metadata' => $metadata,
            'ip_address' => Request::ip() ?? '127.0.0.1',
            'user_agent' => Request::userAgent(),
        ]);
    }
}
