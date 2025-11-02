<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $attachments = $this->input('attachments', []);
        if (is_array($attachments)) {
            $attachments = array_values($attachments);
            $this->merge([
                'attachments' => $attachments,
            ]);
        }

        $this->merge([
            'urgent' => $this->boolean('urgent'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'org_id' => ['required', 'exists:orgs,id'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'min:20'],
            'contact_name' => ['nullable', 'string', 'max:150'],
            'contact_email' => ['nullable', 'email'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'urgent' => ['boolean'],
            'attachments' => ['nullable', 'array'],
            'attachments.*.file' => [
                'nullable',
                'file',
                'max:51200',
                'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,mp3,wav,aac,ogg,m4a,mp4,mov,avi,mpeg,mpg,webm,mkv',
            ],
            'attachments.*.comment' => ['nullable', 'string', 'max:500'],
            'voice_recording' => [
                'nullable',
                'file',
                'max:51200',
                'mimes:mp3,wav,aac,ogg,m4a,webm',
            ],
            'voice_comment' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $attachments = $this->input('attachments', []);
            foreach ($attachments as $index => $attachment) {
                $comment = $attachment['comment'] ?? null;
                $hasComment = is_string($comment) && trim($comment) !== '';
                $file = $this->file("attachments.$index.file");

                if ($hasComment && ! $file) {
                    $validator->errors()->add("attachments.$index.file", 'Please upload a file for this comment.');
                }
            }

            $voiceComment = $this->input('voice_comment');
            $voiceHasComment = is_string($voiceComment) && trim($voiceComment) !== '';
            if ($voiceHasComment && ! $this->file('voice_recording')) {
                $validator->errors()->add('voice_recording', 'Please record or upload audio before adding a comment.');
            }
        });
    }
}
