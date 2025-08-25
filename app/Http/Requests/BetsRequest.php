<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BetsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<int, string|ValidationRule>>
     *
     * @phpstan-return array<string, ValidationRule|array<int, string|ValidationRule>>
     */
    public function rules(): array
    {
        return [
            'selection_ids' => ['required', 'array', 'min:1'],
            'selection_ids.*' => ['integer', 'distinct', Rule::exists('selections', 'id')],
            'stake' => ['required', 'integer', 'min:1'],
        ];
    }
}
