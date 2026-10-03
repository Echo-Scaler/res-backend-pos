<?php

namespace App\Http\Requests\Admin\Table;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDiningTableRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user && ($user->hasRole('OWNER') || $user->hasRole('MANAGER'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $currentUser = $this->user();
        $tableId = $this->route('table') ? ($this->route('table')->id ?? $this->route('table')) : null;

        return [
            'table_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('dining_tables', 'table_number')
                    ->where(function ($query) use ($currentUser) {
                        $query->where('restaurant_id', $currentUser->restaurant_id);
                    })
                    ->ignore($tableId),
            ],
            'name' => ['nullable', 'string', 'max:100'],
            'seating_capacity' => ['required', 'integer', 'min:1', 'max:50'],
            'floor_area' => ['required', 'string', 'max:100'],
            'status' => [
                'nullable',
                'string',
                Rule::in(['VACANT', 'OCCUPIED', 'ORDERING', 'BILLING', 'RESERVED', 'OUT_OF_SERVICE']),
            ],
            'notes' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Custom validation error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'table_number.required' => 'Table number is required.',
            'table_number.unique' => 'This table number is already used by another table.',
            'seating_capacity.required' => 'Seating capacity is required.',
            'floor_area.required' => 'Dining floor area / zone is required.',
        ];
    }
}
