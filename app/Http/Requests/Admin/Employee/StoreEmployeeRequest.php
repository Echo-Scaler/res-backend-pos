<?php

namespace App\Http\Requests\Admin\Employee;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeRequest extends FormRequest
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
        $user = $this->user();
        $allowedRoles = $user->hasRole('OWNER')
            ? ['MANAGER', 'CASHIER', 'STAFF']
            : ['CASHIER', 'STAFF'];

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'string', Rule::in($allowedRoles)],
            'password' => ['required', 'string', 'min:6'],
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
            'role.required' => 'Please assign a role (Manager, Cashier, or Staff).',
            'role.in' => 'You do not have permission to assign this designated role.',
            'password.required' => 'A password of at least 6 characters is required.',
            'pin_code.digits_between' => 'The POS Quick PIN must be between 4 and 6 numeric digits.',
        ];
    }
}
