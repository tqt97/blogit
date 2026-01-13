<?php

namespace Modules\Categories\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Categories\Application\Queries\ListCategoriesQuery;

class ListCategoriesRequest extends FormRequest
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
            'search' => 'nullable|string|max:100',
            'perPage' => 'nullable|integer|min:1|max:100',
            'sort' => 'nullable|string|in:id,name,slug,created_at',
            'direction' => 'nullable|string|in:asc,desc',
        ];
    }

    public function toQuery(): ListCategoriesQuery
    {
        return new ListCategoriesQuery(
            search: $this->input('search'),
            perPage: $this->integer('perPage', 5),
            sort: $this->input('sort', 'id'),
            direction: $this->input('direction', 'desc'),
        );
    }
}
