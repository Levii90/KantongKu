<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportRangeRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'start_date' => $this->query('start_date', now()->startOfMonth()->toDateString()),
            'end_date' => $this->query('end_date', now()->toDateString()),
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ];
    }
}
