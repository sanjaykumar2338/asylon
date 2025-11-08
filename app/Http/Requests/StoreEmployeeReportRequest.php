<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreEmployeeReportRequest extends StoreReportRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();

        $type = $this->input('type');

        if (! in_array($type, ['hr', 'commendation'], true)) {
            $this->merge([
                'type' => 'hr',
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = parent::rules();

        $rules['recipients'] = ['required', 'array', 'min:1'];
        $rules['recipients.*'] = ['integer', 'exists:org_alert_contacts,id'];

        return $rules;
    }
}
