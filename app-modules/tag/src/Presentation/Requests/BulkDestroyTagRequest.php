<?php

namespace Modules\Tag\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkDestroyTagRequest extends FormRequest
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
        return [
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['bail', 'required', 'integer', 'min:1', 'distinct'],
        ];
    }

    public function messages(): array
    {
        return [
            'ids.required' => 'Please select at least one tag.',
            'ids.array' => 'Invalid ids format.',
            'ids.min' => 'Please select at least one tag.',
            'ids.*.integer' => 'Invalid tag id.',
            'ids.*.min' => 'Invalid tag id.',
            'ids.*.distinct' => 'Duplicate tag id.',
        ];
    }
}
