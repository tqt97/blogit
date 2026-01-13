<?php

declare(strict_types=1);

namespace Modules\Categories\Application\QueryHandlers;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Categories\Application\DTOs\CategoryDTO;
use Modules\Categories\Application\Queries\ListCategoriesQuery;
use Modules\Categories\Infrastructure\Persistence\Eloquent\Models\CategoryModel;
use Modules\Shared\Traits\AppliesSearchSortFilter;

final class ListCategoriesHandler
{
    use AppliesSearchSortFilter;

    private array $searchableFields = ['name', 'slug'];

    private array $sortableFields = ['id', 'name', 'slug', 'created_at'];

    public function handle(ListCategoriesQuery $query): LengthAwarePaginator
    {
        $builder = CategoryModel::query()
            ->whereNull('parent_id')
            ->with('childrenRecursive')
            ->select([
                'id',
                'name',
                'slug',
                'parent_id',
                'description',
                'is_active',
                'created_at',
            ]);

        $paginator = $this->apply($builder, [
            'search' => $query->search,
            'searchable' => $this->searchableFields,
            'sort' => $query->sort,
            'sortable' => $this->sortableFields,
            'direction' => $query->direction,
            'per_page' => $query->perPage,
        ]);

        // Mapping to DTO
        $paginator->setCollection(
            $paginator->getCollection()->map(fn (CategoryModel $model) => $this->mapModelToDTO($model))
        );

        return $paginator;
    }

    private function mapModelToDTO(CategoryModel $model): CategoryDTO
    {
        $children = null;
        if ($model->relationLoaded('childrenRecursive') && $model->childrenRecursive) {
            $children = $model->childrenRecursive->map(fn (CategoryModel $child) => $this->mapModelToDTO($child))->all();
        }

        return new CategoryDTO(
            id: $model->id,
            name: $model->name,
            slug: $model->slug,
            parent_id: $model->parent_id,
            description: $model->description,
            is_active: (bool) $model->is_active->value,
            created_at: $model->created_at->toDateTimeString(),
            children_recursive: $children
        );
    }
}
