<?php

namespace App\Http\Requests\Admin\Roles;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ToggleRolePermissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user && $user->hasRole('OWNER');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'role' => [
                'required',
                'string',
                Rule::in(['MANAGER', 'CASHIER', 'STAFF']),
            ],
            'permission' => [
                'required',
                'string',
                Rule::exists('permissions', 'name'),
            ],
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
            'role.required' => 'Target role is required.',
            'role.in' => 'Owner permissions cannot be altered. You can only customize Manager, Cashier, or Staff roles.',
            'permission.required' => 'Permission name is required.',
            'permission.exists' => 'The specified permission does not exist in the RBAC system.',
        ];
    }
}
