<?php

namespace App\Http\Requests;

use App\Models\ReportCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReportRequest extends FormRequest
{
    /**
     * @var array<string, array<int, string>>
     */
    protected array $categoryMap = [];

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

        if (blank($this->input('violation_date'))) {
            $this->merge([
                'violation_date' => null,
            ]);
        }

        if ($this->filled('org_code')) {
            $this->merge([
                'org_code' => strtoupper(trim((string) $this->input('org_code'))),
            ]);
        }
    }

    /**
     * @return array<string, array<int, string>>
     */
    protected function getCategoryMap(): array
    {
        if ($this->categoryMap === []) {
            $this->categoryMap = ReportCategory::query()
                ->with('subcategories')
                ->orderBy('position')
                ->orderBy('name')
                ->get()
                ->mapWithKeys(function (ReportCategory $category): array {
                    $subcategories = $category->subcategories
                        ->map(fn ($sub) => $sub->name)
                        ->all();

                    return [$category->name => $subcategories];
                })
                ->toArray();
        }

        return $this->categoryMap;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $categories = $this->getCategoryMap();
        $categoryOptions = array_keys($categories);
        $subcategoryOptions = collect($categories)
            ->flatten()
            ->unique()
            ->values()
            ->all();

        $minWords = $this->minimumDescriptionWords();

        return [
            'org_id' => ['nullable', 'required_without:org_code', 'exists:orgs,id'],
            'org_code' => ['nullable', 'required_without:org_id', 'string', 'max:12', 'exists:orgs,org_code'],
            'category' => ['required', 'string', 'max:100', Rule::in($categoryOptions)],
            'subcategory' => ['required', 'string', 'max:100', Rule::in($subcategoryOptions)],
            'description' => [
                'required',
                'string',
                'min:20',
                function (string $attribute, mixed $value, \Closure $fail) use ($minWords): void {
                    $wordCount = str_word_count(trim((string) $value));
                    if ($wordCount < $minWords) {
                        $fail(__('Describe the issue must be at least :count words. You have provided :current words.', [
                            'count' => $minWords,
                            'current' => $wordCount,
                        ]));
                    }
                },
            ],
            'violation_date' => ['nullable', 'date'],
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
                'mimetypes:audio/mpeg,audio/mp3,audio/wav,audio/x-wav,audio/aac,audio/ogg,audio/webm,audio/mp4,audio/x-m4a,video/webm',
            ],
            'voice_comment' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $categories = $this->getCategoryMap();

        $validator->after(function ($validator) use ($categories): void {
            $category = $this->input('category');
            $subcategory = $this->input('subcategory');

            if ($category !== null && $subcategory !== null) {
                $validSubcategories = $categories[$category] ?? null;

                if (! is_array($validSubcategories) || ! in_array($subcategory, $validSubcategories, true)) {
                    $validator->errors()->add('subcategory', 'Please select a valid option for the chosen category.');
                }
            }

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

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'voice_recording.file' => 'The voice recording must be a valid audio file.',
            'voice_recording.mimetypes' => 'The voice recording must be an audio file (MP3, WAV, AAC, OGG, M4A, or WEBM).',
            'category.in' => 'Select a valid category option.',
            'subcategory.in' => 'Select a valid subcategory option.',
            'org_id.required_without' => 'Please select an organization or use a direct report link.',
            'org_code.required_without' => 'Please select an organization or use a direct report link.',
            'org_code.exists' => 'That organization link is no longer valid. Request a new link from your administrator.',
        ];
    }

    protected function minimumDescriptionWords(): int
    {
        return (int) config('asylon.reports.description_min_words', 20);
    }
}
