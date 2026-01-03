<?php

namespace Modules\Tag\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTagRequest extends FormRequest
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
        $tagId = (int) $this->route('tag');

        return [
            'name' => ['required', 'string'],
            'slug' => [
                'required',
                'string',
                Rule::unique('tags', 'slug')->ignore($tagId),
            ],
        ];
    }
}
