<?php

declare(strict_types=1);

namespace Modules\Tag\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Tag\Domain\ValueObjects\Intent;
use Modules\Tag\Presentation\Rules\TagSlugRule;

class StoreTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['bail', 'required', 'filled', 'string', 'max:255'],
            'slug' => [
                'bail',
                'required',
                'string',
                new TagSlugRule,
                Rule::unique('tags', 'slug'),
            ],
            'intent' => ['nullable',  Rule::enum(Intent::class)],
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge(['intent' => $this->input('intent', 'default')]);
    }
}
