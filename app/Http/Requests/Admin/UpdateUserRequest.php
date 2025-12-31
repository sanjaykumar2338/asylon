<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && ($user->isSuperAdmin() || $user->hasRole(['platform_admin', 'org_admin']));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::in(['super_admin', 'platform_admin', 'org_admin', 'security_lead', 'reviewer', 'org_user', 'executive_admin'])],
            'org_id' => ['nullable', 'exists:orgs,id'],
            'active' => ['sometimes', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $role = $this->input('role');
            $orgId = $this->input('org_id');

            if (in_array($role, ['super_admin', 'platform_admin'], true) && $orgId) {
                $validator->errors()->add('org_id', 'Global roles cannot be assigned to an organization.');
            }

            if (in_array($role, ['org_admin', 'executive_admin', 'security_lead', 'reviewer', 'org_user'], true) && empty($orgId)) {
                $validator->errors()->add('org_id', 'An organization is required for this role.');
            }
        });
    }
}
