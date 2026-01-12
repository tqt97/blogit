<?php

namespace Modules\Shared\Traits;

use Illuminate\Database\Eloquent\Builder;

trait AppliesSearchSortFilter
{
    /**
     * Apply search, sorting, and pagination to an Eloquent query.
     */
    protected function apply(Builder $query, array $params)
    {
        // 1. Apply Search
        if (! empty($params['search']) && ! empty($params['searchable'])) {
            $searchTerm = trim($params['search']);
            $query->where(function (Builder $q) use ($searchTerm, $params) {
                foreach ($params['searchable'] as $field) {
                    $q->orWhere($field, 'like', "%{$searchTerm}%");
                }
            });
        }

        // 2. Apply Sorting
        $sortField = $params['sort'] ?? 'id';
        $sortDirection = $params['direction'] ?? 'desc';

        if (! empty($params['sortable']) && in_array($sortField, $params['sortable'], true)) {
            $query->orderBy($sortField, strtolower($sortDirection) === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('id', 'desc');
        }

        // 3. Apply Pagination
        $perPage = $params['per_page'] ?? 15;

        return $query->paginate($perPage)->withQueryString();
    }
}
