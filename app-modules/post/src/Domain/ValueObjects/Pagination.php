<?php

declare(strict_types=1);

namespace Modules\Post\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class Pagination
{
    public function __construct(
        public int $page,
        public int $perPage,
        public int $maxPerPage = 100,
    ) {
        if ($this->page < 1) {
            throw new InvalidArgumentException('Page must be >= 1.');
        }
    }

    public static function fromInts(?int $page, ?int $perPage, int $default = 15): self
    {
        return new self($page ?? 1, $perPage ?? $default);
    }
}
