<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDemoFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'organization' => ['required', 'string', 'max:255'],
            'organization_type' => ['required', 'string', 'in:School,Church,Workplace,Other'],
            'role' => ['nullable', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:80'],
            'meeting' => ['required', 'string', 'in:15-minute intro,30-minute full demo'],
            'time_window' => ['nullable', 'string', 'in:Morning,Afternoon,Evening'],
            'concerns' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
