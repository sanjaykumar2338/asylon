<?php

namespace App\Http\Requests;

class StoreStudentReportRequest extends StoreReportRequest
{
    /**
     * Force the report type for student submissions.
     */
    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();

        $this->merge([
            'type' => 'safety',
        ]);
    }
}
