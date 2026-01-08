<?php

namespace Modules\Tag\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Tag\Presentation\Rules\TagSlugRule;

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
            'name' => ['bail', 'required', 'filled', 'string', 'max:255'],
            'slug' => [
                'bail',
                'required',
                'string',
                new TagSlugRule,
                Rule::unique('tags', 'slug')->ignore($tagId),
            ],
        ];
    }
}
