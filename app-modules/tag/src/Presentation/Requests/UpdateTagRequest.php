<?php

declare(strict_types=1);

namespace Modules\Tag\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\Tag\Domain\ValueObjects\TagName;
use Modules\Tag\Domain\ValueObjects\TagSlug;

class UpdateTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tag = $this->route('tag');
        $tagId = is_object($tag) ? (int) $tag->getKey() : (int) $tag;

        return [
            'name' => ['bail', 'required', 'filled', 'string', 'max:'.TagName::MAX_LENGTH],
            'slug' => [
                'bail',
                'required',
                'string',
                'max:'.TagSlug::MAX_LENGTH,
                'regex:'.TagSlug::REGEX,
                Rule::unique('tags', 'slug')->ignore($tagId),
            ],
        ];
    }

    public function prepareForValidation(): void
    {
        if ($this->has('slug')) {
            $this->merge([
                'slug' => Str::slug($this->input('slug')),
            ]);
        }
    }
}
