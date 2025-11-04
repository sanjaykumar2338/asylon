<?php

namespace App\Http\Requests;

use App\Models\ReportCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReportRequest extends FormRequest
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
        return $this->user()?->can('review-reports') ?? false;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'urgent' => $this->boolean('urgent'),
        ]);

        if (blank($this->input('violation_date'))) {
            $this->merge([
                'violation_date' => null,
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

        return [
            'category' => ['required', 'string', 'max:100', Rule::in($categoryOptions)],
            'subcategory' => ['required', 'string', 'max:100', Rule::in($subcategoryOptions)],
            'description' => ['required', 'string', 'min:10'],
            'violation_date' => ['nullable', 'date'],
            'contact_name' => ['nullable', 'string', 'max:150'],
            'contact_email' => ['nullable', 'email'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'status' => ['required', 'in:open,in_review,closed'],
            'urgent' => ['boolean'],
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
            'category.in' => 'Select a valid category option.',
            'subcategory.in' => 'Select a valid subcategory option.',
        ];
    }
}
