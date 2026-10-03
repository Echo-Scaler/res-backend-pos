<?php

namespace App\Http\Requests\Admin\Table;

use App\Models\DiningTable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class TransferDiningTableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('target_table_number') && ! $this->filled('target_table_id')) {
            $restaurant = $this->user()?->restaurant;
            if ($restaurant) {
                $target = DiningTable::where('restaurant_id', $restaurant->id)
                    ->where('table_number', trim((string) $this->input('target_table_number')))
                    ->first();
                if ($target) {
                    $this->merge(['target_table_id' => $target->id]);
                }
            }
        }
    }

    public function rules(): array
    {
        return [
            'target_table_id' => ['required', 'integer', 'exists:dining_tables,id'],
            'target_table_number' => ['nullable', 'string', 'max:50'],
            'reason' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $sourceTable = $this->route('table');
            if (! $sourceTable instanceof DiningTable) {
                return;
            }

            $restaurant = $this->user()->restaurant;
            if (! $restaurant || $sourceTable->restaurant_id !== $restaurant->id) {
                $validator->errors()->add('source_table', 'Unauthorized access to this source table.');

                return;
            }

            $targetTableId = (int) $this->input('target_table_id');
            if ($targetTableId === $sourceTable->id) {
                $validator->errors()->add('target_table_id', 'Target table must be different from the current table.');

                return;
            }

            $targetTable = DiningTable::where('restaurant_id', $restaurant->id)->find($targetTableId);
            if (! $targetTable) {
                $validator->errors()->add('target_table_id', 'The selected target table does not belong to this restaurant.');

                return;
            }

            if (! $targetTable->is_active) {
                $validator->errors()->add('target_table_id', "Table {$targetTable->table_number} is currently marked inactive.");

                return;
            }

            if ($targetTable->status !== 'VACANT') {
                $validator->errors()->add('target_table_id', "Table {$targetTable->table_number} is currently {$targetTable->status}. Please select an available VACANT table.");
            }
        });
    }

    public function messages(): array
    {
        return [
            'target_table_id.required' => 'Please select a new target table to move the customer to.',
            'target_table_id.exists' => 'The selected target table was not found in the system.',
            'reason.max' => 'The reason note cannot exceed 255 characters.',
        ];
    }
}
