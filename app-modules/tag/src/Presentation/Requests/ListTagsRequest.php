<?php

namespace Modules\Tag\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListTagsRequest extends FormRequest
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
            'search' => ['nullable', 'string', 'max:255'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'sort' => ['nullable', 'string'],
            'direction' => ['nullable', 'string'],
        ];
    }

    public function filters(): array
    {
        $data = $this->validated();

        return [
            'search' => $data['search'] ?? null,
            'page' => (int) ($data['page'] ?? 1),
            'per_page' => (int) ($data['per_page'] ?? 15),
            'sort' => (string) ($data['sort'] ?? 'id'),
            'direction' => (string) ($data['direction'] ?? 'desc'),
        ];
    }

    protected function prepareForValidation(): void
    {
        $allowedSorts = ['id', 'name', 'slug', 'created_at', 'updated_at'];

        $search = $this->input('search');
        $search = is_string($search) ? trim($search) : null;
        $search = ($search !== '') ? $search : null;

        $sort = (string) ($this->input('sort') ?? 'id');
        $sort = in_array($sort, $allowedSorts, true) ? $sort : 'id';

        $direction = strtolower((string) ($this->input('direction') ?? 'desc'));
        $direction = in_array($direction, ['asc', 'desc'], true) ? $direction : 'desc';

        $page = (int) ($this->input('page') ?? 1);
        $perPage = (int) ($this->input('per_page') ?? 15);

        $this->merge([
            'search' => $search,
            'sort' => $sort,
            'direction' => $direction,
            'page' => max(1, $page),
            'per_page' => min(100, max(1, $perPage)),
        ]);
    }
}
