<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrgRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && $user->hasRole('platform_admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['required', 'string', 'max:140', Rule::unique('orgs', 'slug')],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'default_locale' => ['required', 'string', Rule::in(config('app.supported_locales', ['en']))],
            'enable_commendations' => ['boolean'],
            'enable_hr_reports' => ['boolean'],
            'enable_student_reports' => ['boolean'],
            'enable_ultra_private_mode' => ['boolean'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'enable_commendations' => $this->boolean('enable_commendations'),
            'enable_hr_reports' => $this->boolean('enable_hr_reports'),
            'enable_student_reports' => $this->boolean('enable_student_reports', true),
            'enable_ultra_private_mode' => $this->boolean('enable_ultra_private_mode'),
        ]);
    }
}
