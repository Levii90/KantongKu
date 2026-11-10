<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['sometimes', 'nullable', 'in:income,expense'],
            'category_id' => ['sometimes', 'nullable', 'integer', 'exists:categories,id'],
            'month' => ['sometimes', 'nullable', 'integer', 'between:1,12'],
            'year' => ['sometimes', 'nullable', 'integer', 'min:2000'],
        ];
    }
}
