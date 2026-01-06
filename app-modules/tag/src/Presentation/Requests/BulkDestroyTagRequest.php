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
            'ids.*' => ['integer', 'min:1'],
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
        ];
    }

    protected function prepareForValidation(): void
    {
        $raw = $this->input('ids');

        if (! is_array($raw)) {
            $raw = [];
        }

        $ids = array_values(array_unique(array_filter(array_map(
            static fn ($v) => filter_var($v, FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 1],
            ]) ?: null,
            $raw
        ))));

        $this->merge(['ids' => $ids]);
    }

    public function ids(): array
    {
        return array_map('intval', $this->validated('ids'));
    }
}
