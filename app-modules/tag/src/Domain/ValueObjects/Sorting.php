<?php

declare(strict_types=1);

namespace Modules\Tag\Domain\ValueObjects;

final readonly class Sorting
{
    public function __construct(
        public SortField $field = SortField::Id,
        public SortDirection $direction = SortDirection::Desc,
    ) {}
}
