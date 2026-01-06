<?php

declare(strict_types=1);

namespace Modules\Tag\Application\QueryHandlers;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Categories\Application\Queries\ListCategoriesQuery;
use Modules\Categories\Infrastructure\Persistence\Eloquents\Models\CategoryModel;
use Modules\Category\Application\DTOs\CategoryDTO;
use Modules\Tag\Application\DTOs\TagDTO;
use Modules\Tag\Infrastructure\Persistence\Eloquent\Models\TagModel;

final class ListTagsHandler
{
    /** @return LengthAwarePaginator<TagDTO> */
    public function handle(ListCategoriesQuery $q): LengthAwarePaginator
    {
        $builder = CategoryModel::query()->select(['id', 'name', 'slug', 'created_at']);

        if ($q->search) {
            $s = trim($q->search);
            $builder->where(function ($w) use ($s) {
                $w->where('name', 'like', "%{$s}%")
                    ->orWhere('slug', 'like', "%{$s}%");
            });
        }

        $sort = in_array($q->sort, ['id', 'name', 'slug', 'created_at'], true) ? $q->sort : 'id';
        $dir = strtolower($q->direction) === 'asc' ? 'asc' : 'desc';

        $p = $builder->orderBy($sort, $dir)->paginate($q->perPage);

        // map items -> DTO
        $p->setCollection(
            $p->getCollection()->map(fn($m) => new CategoryDTO((int) $m->id, (string) $m->name, (string) $m->slug))
        );

        return $p;
    }
}