<?php

declare(strict_types=1);

namespace Modules\Tag\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class Pagination
{
    public const DEFAULT_PAGE = 1;

    public const DEFAULT_PER_PAGE = 15;

    public const MAX_PER_PAGE = 100;

    public function __construct(
        public int $page = self::DEFAULT_PAGE,
        public int $perPage = self::DEFAULT_PER_PAGE,
    ) {
        if ($this->page < 1) {
            throw new InvalidArgumentException('Page must be >= 1.');
        }

        if ($this->perPage < 1 || $this->perPage > self::MAX_PER_PAGE) {
            throw new InvalidArgumentException('PerPage out of range.');
        }
    }

    public static function fromInts(?int $page, ?int $perPage): self
    {
        return new self(
            page: max(1, $page ?? self::DEFAULT_PAGE),
            perPage: min(self::MAX_PER_PAGE, max(1, $perPage ?? self::DEFAULT_PER_PAGE)),
        );
    }
}
