<?php

declare(strict_types=1);

namespace Modules\Tag\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\Tag\Domain\ValueObjects\Intent;
use Modules\Tag\Domain\ValueObjects\TagName;
use Modules\Tag\Domain\ValueObjects\TagSlug;

class StoreTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['bail', 'required', 'filled', 'string', 'max:'.TagName::MAX_LENGTH],
            'slug' => [
                'bail',
                'required',
                'string',
                'max:'.TagSlug::MAX_LENGTH,
                'regex:'.TagSlug::REGEX,
                Rule::unique('tags', 'slug'),
            ],
            'intent' => ['nullable',  Rule::enum(Intent::class)],
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'intent' => $this->input('intent', 'default'),
        ]);

        if ($this->has('slug')) {
            $this->merge([
                'slug' => Str::slug($this->input('slug')),
            ]);
        }
    }
}
