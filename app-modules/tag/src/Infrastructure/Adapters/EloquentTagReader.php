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
        $q = TagModel::query()->select(['id', 'name', 'slug', 'created_at', 'updated_at']);

        if ($search !== null && trim($search) !== '') {
            $s = trim($search);
            $q->where('name', 'like', "%{$s}%");
        }

        $p = $q->orderBy($sort, $direction)
            ->paginate(perPage: $perPage, page: $page)
            ->withQueryString();

        // Map paginator items to TagDTO
        $p->setCollection(
            $p->getCollection()->map(fn ($m) => new TagDTO(
                id: (int) $m->id,
                name: (string) $m->name,
                slug: (string) $m->slug,
                created_at: $m->created_at?->toISOString() ?? '',
                updated_at: $m->updated_at?->toISOString() ?? '',
            ))
        );

        return $p;
    }

    public function find(int $id): ?TagDTO
    {
        $m = TagModel::query()
            ->select(['id', 'name', 'slug', 'created_at', 'updated_at'])
            ->find($id);

        if (! $m) {
            return null;
        }

        return new TagDTO(
            id: (int) $m->id,
            name: (string) $m->name,
            slug: (string) $m->slug,
            created_at: $m->created_at?->toISOString() ?? '',
            updated_at: $m->updated_at?->toISOString() ?? '',
        );
    }
}
