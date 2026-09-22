<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserRoleResource extends JsonResource
{
    /**
     * Transform the user into a secure JSON resource.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'restaurant_id' => $this->restaurant_id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->getRoleNames()->first() ?? 'STAFF',
            'permissions' => $this->getAllPermissions()->pluck('name')->values()->all(),
            'direct_permissions' => $this->getDirectPermissions()->pluck('name')->values()->all(),
            'role_permissions' => $this->getPermissionsViaRoles()->pluck('name')->values()->all(),
            'status' => 'Active',
            'has_pin' => ! empty($this->pin_code),
            'created_at' => $this->created_at?->toIso8601String(),
            'formatted_created_at' => $this->created_at?->format('M d, Y') ?? 'Jan 01, 2026',
        ];
    }
}
