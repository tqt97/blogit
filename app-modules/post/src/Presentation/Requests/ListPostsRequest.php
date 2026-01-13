<?php

declare(strict_types=1);

namespace Modules\Post\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Post\Domain\ValueObjects\SortDirection;
use Modules\Post\Domain\ValueObjects\SortField;
use Modules\Post\Domain\ValueObjects\TrashedFilter;

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
            'trashed' => ['nullable', Rule::in(array_map(fn ($c) => $c->value, TrashedFilter::cases()))],
            'category_id' => ['nullable', 'integer'],
            'tag_id' => ['nullable', 'integer'],
            'author_id' => ['nullable', 'integer'],
        ];
    }

    public function filters(): array
    {
        $validated = $this->validated();

        // Define defaults
        $defaults = [
            'page' => 1,
            'per_page' => (int) config('post.pagination.default_per_page'),
            'sort' => SortField::Id->value,
            'direction' => SortDirection::Desc->value,
            'trashed' => TrashedFilter::Without->value,
        ];

        $filters = [];

        // 1. Process Search (Trim and nullify if empty)
        $search = $this->input('search');
        if (is_string($search) && trim($search) !== '') {
            $filters['search'] = trim($search);
        }

        // 2. Process Numeric Filters (Category, Tag, Author)
        foreach (['category_id', 'tag_id', 'author_id'] as $key) {
            if ($this->filled($key)) {
                $filters[$key] = $this->integer($key);
            }
        }

        // 3. Process Pagination & Sorting (Only if they differ from defaults)
        foreach ($defaults as $key => $defaultValue) {
            $value = $this->input($key);

            // Special handling for integer keys
            if (in_array($key, ['page', 'per_page'])) {
                $value = $this->filled($key) ? $this->integer($key) : null;
            }

            if ($value !== null && $value !== '' && (string) $value !== (string) $defaultValue) {
                $filters[$key] = $value;
            }
        }

        return $filters;
    }
}
