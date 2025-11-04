<?php

namespace App\Http\Requests\Admin;

use App\Models\ReportCategory;
use App\Models\ReportSubcategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReportSubcategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('manage-org') ?? false;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('name')) {
            $this->merge([
                'name' => trim((string) $this->input('name')),
            ]);
        }

        if ($this->has('description')) {
            $this->merge([
                'description' => trim((string) $this->input('description')) ?: null,
            ]);
        }
    }

    /**
     * @return ReportCategory|null
     */
    protected function category(): ?ReportCategory
    {
        return $this->route('report_category');
    }

    /**
     * @return ReportSubcategory|null
     */
    protected function subcategory(): ?ReportSubcategory
    {
        return $this->route('report_subcategory');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $categoryId = $this->category()?->id ?? 0;
        $subcategory = $this->subcategory();

        return [
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('report_subcategories', 'name')
                    ->where(fn ($query) => $query->where('report_category_id', $categoryId))
                    ->ignore($subcategory?->id),
            ],
            'description' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ];
    }
}
