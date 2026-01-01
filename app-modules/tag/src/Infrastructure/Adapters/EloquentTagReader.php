<?php declare(strict_types=1);

namespace Modules\Tag\Infrastructure\Adapters;

use Modules\Tag\Application\Contracts\TagReaderInterface;
use Modules\Tag\Infrastructure\Persistence\Eloquent\Models\Tag;

final class EloquentTagReader implements TagReaderInterface
{
    public function filterExistingIds(array $ids): array
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));
        if ($ids === [])
            return [];

        return Tag::query()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->map(static fn($id) => (int) $id)
            ->all();
    }

    public function listForSelect(): array
    {
        return Tag::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get()
            ->map(static fn($t) => [
                'id' => (int) $t->id,
                'label' => (string) $t->name,
            ])
            ->all();
    }
}
