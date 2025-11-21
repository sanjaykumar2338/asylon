<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrgRequest extends FormRequest
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
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('on_call_user_id') && $this->input('on_call_user_id') === '') {
            $this->merge([
                'on_call_user_id' => null,
            ]);
        }

        $this->merge([
            'enable_commendations' => $this->boolean('enable_commendations'),
            'enable_hr_reports' => $this->boolean('enable_hr_reports'),
            'enable_student_reports' => $this->boolean('enable_student_reports', true),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $org = $this->route('org');
        $orgId = $org ? $org->id : null;

        $onCallRules = ['nullable'];

        if ($orgId) {
            $onCallRules[] = Rule::exists('users', 'id')->where(function ($query) use ($orgId) {
                $query->where('org_id', $orgId)
                    ->whereIn('role', ['reviewer', 'security_lead'])
                    ->where('active', true);
            });
        }

        return [
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['required', 'string', 'max:140', Rule::unique('orgs', 'slug')->ignore($orgId)],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'default_locale' => ['required', 'string', Rule::in(config('app.supported_locales', ['en']))],
            'on_call_user_id' => $onCallRules,
            'enable_commendations' => ['boolean'],
            'enable_hr_reports' => ['boolean'],
            'enable_student_reports' => ['boolean'],
        ];
    }
}
