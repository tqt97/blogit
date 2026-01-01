<?php

namespace Modules\Post\DTOs;

class PostFilterDTO
{
    public function __construct(
        public readonly ?string $q = null,
        public readonly ?string $status = null,
        public readonly ?int $categoryId = null,
        public readonly ?int $tagId = null,
        public readonly string $sort = 'published_at',
        public readonly string $direction = 'desc',
        public readonly int $perPage = 15,
    ) {}

    public static function fromArray(array $data): self
    {
        $q = isset($data['q']) ? trim((string) $data['q']) : null;
        $status = isset($data['status']) && $data['status'] !== '' ? (string) $data['status'] : null;

        $categoryId = isset($data['category_id']) && $data['category_id'] !== ''
            ? (int) $data['category_id']
            : null;

        $tagId = isset($data['tag_id']) && $data['tag_id'] !== ''
            ? (int) $data['tag_id']
            : null;

        $sort = in_array(($data['sort'] ?? ''), ['published_at', 'created_at', 'title'], true)
            ? (string) $data['sort']
            : 'published_at';

        $direction = strtolower((string) ($data['direction'] ?? 'desc'));
        $direction = in_array($direction, ['asc', 'desc'], true) ? $direction : 'desc';

        $perPage = (int) ($data['per_page'] ?? 15);
        $perPage = max(5, min(100, $perPage));

        return new self($q ?: null, $status, $categoryId, $tagId, $sort, $direction, $perPage);
    }
}
