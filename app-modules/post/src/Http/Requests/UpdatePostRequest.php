<?php

namespace Modules\Post\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Post\Rules\CategoryExists;
use Modules\Post\Rules\TagExists;
use Modules\Shared\Contracts\Taxonomy\CategoryLookup;
use Modules\Shared\Contracts\Taxonomy\TagLookup;

class UpdatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $postId = $this->route('post')?->id ?? $this->route('post'); // hỗ trợ route model binding

        return [
            'title' => ['required', 'string', 'max:220'],
            'slug' => [
                'required', 'string', 'max:240',
                Rule::unique('posts', 'slug')->ignore($postId),
            ],
            'content' => ['required', 'string'],
            'excerpt' => ['nullable', 'string', 'max:5000'],

            'category_id' => ['nullable', new CategoryExists(app(CategoryLookup::class))],

            'tag_ids' => ['nullable', new TagExists(app(TagLookup::class))],
            'tag_ids.*' => ['integer', 'min:1'],

            'status' => ['required', 'in:draft,pending,published'],
            'published_at' => ['nullable', 'date'],
        ];
    }
}
