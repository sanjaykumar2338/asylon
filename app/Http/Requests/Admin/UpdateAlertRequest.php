<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAlertRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && $user->hasRole(['platform_admin', 'org_admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['email', 'sms'])],
            'value' => ['required', 'string', 'max:160'],
            'org_id' => ['required', 'exists:orgs,id'],
            'department' => ['required', Rule::in(array_keys(config('asylon.alerts.departments')))],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
