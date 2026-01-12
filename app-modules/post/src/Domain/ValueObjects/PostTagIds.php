<?php

declare(strict_types=1);

namespace Modules\Post\Domain\ValueObjects;

final class PostTagIds
{
    /** @var int[] */
    private array $ids;

    public function __construct(array $ids)
    {
        $this->ids = array_unique(array_filter(array_map('intval', $ids), fn ($id) => $id > 0));
    }

    public function ids(): array
    {
        return $this->ids;
    }
}
