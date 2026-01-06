<?php

declare(strict_types=1);

namespace Modules\Tag\Infrastructure\Adapters;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Tag\Application\Contracts\TagReader;
use Modules\Tag\Application\DTOs\TagDTO;
use Modules\Tag\Infrastructure\Persistence\Eloquent\Models\TagModel;

final class EloquentTagReader implements TagReader
{
    public function paginate(?string $search, int $page, int $perPage, string $sort, string $direction): LengthAwarePaginator
    {
        $query = TagModel::query()->select(['id', 'name', 'slug', 'created_at', 'updated_at']);

        if ($search !== null && trim($search) !== '') {
            $s = trim($search);
            $query->where('name', 'like', "%{$s}%");
        }

        $tags = $query->orderBy($sort, $direction)
            ->paginate(perPage: $perPage, page: $page)
            ->withQueryString();

        // Map paginator items to TagDTO
        $tags->setCollection(
            $tags->getCollection()->map(fn ($model) => new TagDTO(
                id: (int) $model->id,
                name: (string) $model->name,
                slug: (string) $model->slug,
                created_at: $model->created_at?->toISOString() ?? '',
                updated_at: $model->updated_at?->toISOString() ?? '',
            ))
        );

        return $tags;
    }

    public function find(int $id): ?TagDTO
    {
        $model = TagModel::query()
            ->select(['id', 'name', 'slug', 'created_at', 'updated_at'])
            ->find($id);

        if (! $model) {
            return null;
        }

        return new TagDTO(
            id: (int) $model->id,
            name: (string) $model->name,
            slug: (string) $model->slug,
            created_at: $model->created_at?->toISOString() ?? '',
            updated_at: $model->updated_at?->toISOString() ?? '',
        );
    }
}
