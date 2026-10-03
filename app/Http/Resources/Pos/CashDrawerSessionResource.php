<?php

namespace App\Http\Resources\Pos;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CashDrawerSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'terminal_code' => $this->terminal_code,
            'cashier_id' => $this->user_id,
            'cashier_name' => $this->cashier ? $this->cashier->name : null,
            'status' => $this->status,
            'opened_at' => $this->opened_at ? $this->opened_at->toIso8601String() : null,
            'closed_at' => $this->closed_at ? $this->closed_at->toIso8601String() : null,
            'opening_float' => $this->opening_float,
            'formatted_opening_float' => number_format($this->opening_float, 0).' MMK',
            'cash_sales' => $this->cash_sales,
            'formatted_cash_sales' => number_format($this->cash_sales, 0).' MMK',
            'digital_sales' => $this->digital_sales,
            'formatted_digital_sales' => number_format($this->digital_sales, 0).' MMK',
            'cash_in' => $this->cash_in,
            'formatted_cash_in' => number_format($this->cash_in, 0).' MMK',
            'cash_out' => $this->cash_out,
            'formatted_cash_out' => number_format($this->cash_out, 0).' MMK',
            'expected_cash' => $this->expected_cash,
            'formatted_expected_cash' => number_format($this->expected_cash, 0).' MMK',
            'closing_actual_cash' => $this->closing_actual_cash,
            'formatted_closing_actual_cash' => $this->closing_actual_cash !== null ? number_format($this->closing_actual_cash, 0).' MMK' : null,
            'cash_difference' => $this->cash_difference,
            'formatted_cash_difference' => $this->cash_difference !== null ? number_format($this->cash_difference, 0).' MMK' : null,
            'currency' => 'MMK',
            'currency_symbol' => 'Ks ',
            'notes' => $this->notes,
        ];
    }
}
