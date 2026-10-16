<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountSecurityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $hasPin = ! empty($this->pin_code);
        $has2Fa = ! empty($this->two_factor_secret) && ! is_null($this->two_factor_confirmed_at);
        $hasRecentPassword = ! is_null($this->password_changed_at);

        // Compute security score
        $score = 50; // base score with active strong password
        if ($hasPin) {
            $score += 25;
        }
        if ($has2Fa) {
            $score += 25;
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->roles->first()?->name ?? 'Staff',
            'has_pin_code' => $hasPin,
            'two_factor_enabled' => $has2Fa,
            'two_factor_confirmed_at' => $this->two_factor_confirmed_at?->format('Y-m-d H:i:s'),
            'password_changed_at' => $this->password_changed_at?->format('Y-m-d H:i:s'),
            'security_score' => $score,
            'security_level' => $score >= 100 ? 'High' : ($score >= 75 ? 'Medium' : 'Standard'),
            'restaurant' => [
                'id' => $this->restaurant?->id,
                'name' => $this->restaurant?->name,
            ],
            'sessions' => $this->additional['sessions'] ?? [],
            'active_sessions_count' => count($this->additional['sessions'] ?? []),
            'last_backup_info' => $this->additional['last_backup_info'] ?? null,
        ];
    }
}
