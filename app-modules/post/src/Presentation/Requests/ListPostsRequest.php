<?php

declare(strict_types=1);

namespace Modules\Post\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Post\Domain\ValueObjects\SortDirection;
use Modules\Post\Domain\ValueObjects\SortField;

class ListPostsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:'.config('post.pagination.max_per_page')],
            'sort' => ['nullable', Rule::in(array_map(fn ($c) => $c->value, SortField::cases()))],
            'direction' => ['nullable', Rule::in(array_map(fn ($c) => $c->value, SortDirection::cases()))],
        ];
    }

    public function filters(): array
    {
        $this->validated();

        $search = $this->input('search');
        $search = is_string($search) ? trim($search) : null;
        $search = ($search === '') ? null : $search;

        return [
            'search' => $search,
            'page' => $this->integer('page', 1),
            'per_page' => $this->integer('per_page', (int) config('post.pagination.default_per_page')),
            'sort' => $this->input('sort', SortField::Id->value),
            'direction' => $this->input('direction', SortDirection::Desc->value),
        ];
    }
}
