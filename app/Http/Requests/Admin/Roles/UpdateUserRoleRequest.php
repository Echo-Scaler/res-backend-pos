<?php

namespace App\Http\Requests\Admin\Roles;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $currentUser = $this->user();

        return $currentUser && $currentUser->hasRole('OWNER');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $currentUser = $this->user();

        return [
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) use ($currentUser) {
                    $query->where('restaurant_id', $currentUser->restaurant_id);
                }),
            ],
            'role' => [
                'required',
                'string',
                Rule::in(['OWNER', 'MANAGER', 'CASHIER', 'STAFF']),
            ],
        ];
    }

    /**
     * Configure additional validator checks.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $currentUser = $this->user();
            $targetUserId = (int) $this->input('user_id');
            $newRole = (string) $this->input('role');

            if ($targetUserId === $currentUser->id && $newRole !== 'OWNER') {
                $validator->errors()->add('role', 'You cannot demote your own account from OWNER authority.');
            }
        });
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'Target user ID is required.',
            'user_id.exists' => 'The selected staff member does not exist in your restaurant.',
            'role.required' => 'Please choose a valid destination role.',
            'role.in' => 'The selected role must be one of OWNER, MANAGER, CASHIER, or STAFF.',
        ];
    }
}
