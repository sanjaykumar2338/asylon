<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostChatMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'min:2', 'max:5000'],
        ];
    }

    /**
     * Support legacy payloads that still reference the body field.
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled('body') && ! $this->filled('message')) {
            $this->merge(['message' => $this->input('body')]);
        }
    }
}
