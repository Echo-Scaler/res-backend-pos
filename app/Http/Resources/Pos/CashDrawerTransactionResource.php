<?php

namespace App\Http\Resources\Pos;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CashDrawerTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'session_id' => $this->cash_drawer_session_id,
            'type' => $this->type,
            'amount' => $this->amount,
            'formatted_amount' => number_format($this->amount, 0).' MMK',
            'currency' => 'MMK',
            'currency_symbol' => 'Ks ',
            'reason' => $this->reason,
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
        ];
    }
}
