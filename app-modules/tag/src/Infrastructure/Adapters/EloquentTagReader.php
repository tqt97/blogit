<?php

declare(strict_types=1);

namespace Modules\Tag\Infrastructure\Adapters;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Tag\Application\Contracts\TagReader;
use Modules\Tag\Application\DTOs\TagDTO;
use Modules\Tag\Domain\ValueObjects\Pagination;
use Modules\Tag\Domain\ValueObjects\SearchTerm;
use Modules\Tag\Domain\ValueObjects\Sorting;
use Modules\Tag\Infrastructure\Persistence\Eloquent\Models\TagModel;

final class EloquentTagReader implements TagReader
{
    public function paginate(?SearchTerm $search, Pagination $pagination, Sorting $sorting): LengthAwarePaginator
    {
        $query = TagModel::query()
            ->select(['id', 'name', 'slug', 'created_at', 'updated_at']);

        if ($search !== null) {
            $s = $search->value;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('slug', 'like', "%{$s}%");
            });
        }

        return $query
            ->orderBy($sorting->field->value, $sorting->direction->value)
            ->paginate(perPage: $pagination->perPage, page: $pagination->page)
            ->withQueryString()
            ->through(fn (TagModel $model) => new TagDTO(
                id: (int) $model->id,
                name: (string) $model->name,
                slug: (string) $model->slug,
                created_at: $model->created_at?->toISOString() ?? '',
                updated_at: $model->updated_at?->toISOString() ?? '',
            ));
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
