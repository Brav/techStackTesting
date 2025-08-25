<?php

namespace App\Http\Requests;

use App\Models\Database\Enums\EventStatusEnum;
use Illuminate\Contracts\Validation\Rule as LegacyRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class EventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|LegacyRule|array<int, string|ValidationRule|LegacyRule>>
     *
     * @phpstan-return array<string, ValidationRule|LegacyRule|array<int, string|ValidationRule|LegacyRule>>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'min:3', 'max:32'],
            'page' => ['nullable', 'integer', 'min:1'],
            'starts_after' => ['nullable', 'date'],
            'status_id' => ['nullable', new Enum(EventStatusEnum::class)],
        ];
    }
}
