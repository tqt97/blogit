<?php

declare(strict_types=1);

namespace Modules\Post\Domain\ValueObjects;

final readonly class Sorting
{
    public function __construct(
        public SortField $field = SortField::Id,
        public SortDirection $direction = SortDirection::Desc,
    ) {}
}
