<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTranslationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'locale' => ['required', 'string', 'max:10'],
            'key' => ['required', 'string', 'max:191'],
            'content' => ['required', 'string'],
            'tags' => ['sometimes', 'array'],
            'tags.*' => ['string', 'max:80'],
        ];
    }
}
