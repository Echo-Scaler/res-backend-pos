<?php

namespace App\Http\Requests\Admin\Employee;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $currentUser = $this->user();
        /** @var User|null $targetEmployee */
        $targetEmployee = $this->route('employee');

        if (! $currentUser || ! $targetEmployee) {
            return false;
        }

        // Must belong to the same restaurant
        if ($targetEmployee->restaurant_id !== $currentUser->restaurant_id) {
            return false;
        }

        // Managers cannot update Owner accounts or another Manager
        if ($currentUser->hasRole('MANAGER')) {
            if ($targetEmployee->hasRole('OWNER') || ($targetEmployee->hasRole('MANAGER') && $targetEmployee->id !== $currentUser->id)) {
                return false;
            }
        }

        return $currentUser->hasRole('OWNER') || $currentUser->hasRole('MANAGER');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $currentUser = $this->user();
        /** @var User $targetEmployee */
        $targetEmployee = $this->route('employee');

        $allowedRoles = $currentUser->hasRole('OWNER')
            ? ($targetEmployee->hasRole('OWNER') ? ['OWNER'] : ['MANAGER', 'CASHIER', 'STAFF'])
            : ['CASHIER', 'STAFF'];

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($targetEmployee->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'string', Rule::in($allowedRoles)],
            'password' => ['nullable', 'string', 'min:6'],
            'pin_code' => ['nullable', 'digits_between:4,6'],
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
            'name.required' => 'Please enter the employee full name.',
            'email.required' => 'An email address is required for POS Back-Office login.',
            'email.unique' => 'This email is already registered in the system.',
            'role.required' => 'Please assign a designated role.',
            'role.in' => 'You do not have permission to assign this designated role.',
            'password.min' => 'If updating password, it must be at least 6 characters.',
            'pin_code.digits_between' => 'The POS Quick PIN must be between 4 and 6 numeric digits.',
        ];
    }
}
