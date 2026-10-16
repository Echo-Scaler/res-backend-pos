<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuditLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user_name' => $this->user_name ?? 'System Automated',
            'role_name' => $this->role_name ?? 'N/A',
            'category' => $this->category ?? 'GENERAL',
            'action' => $this->action,
            'severity' => $this->severity ?? 'INFO',
            'severity_color' => $this->severity_color ?? 'info',
            'resource_type' => $this->resource_type,
            'resource_id' => $this->resource_id,
            'old_value' => $this->old_value,
            'new_value' => $this->new_value,
            'metadata' => $this->metadata ?? [],
            'ip_address' => $this->ip_address ?? '127.0.0.1',
            'user_agent' => $this->user_agent,
            'currency' => 'MMK',
            'currency_symbol' => 'Ks ',
            'created_at_human' => $this->created_at?->diffForHumans(),
            'created_at_formatted' => $this->created_at?->format('d/m/Y h:i:s A'),
            'created_at_date' => $this->created_at?->format('Y-m-d'),
            'created_at_time' => $this->created_at?->format('h:i:s A'),
        ];
    }
}
