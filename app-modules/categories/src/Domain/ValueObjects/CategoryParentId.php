<?php

declare(strict_types=1);

namespace Modules\Categories\Domain\ValueObjects;

use InvalidArgumentException;

final class CategoryParentId
{
    public function __construct(private int $value)
    {
        if ($value <= 0) {
            throw new InvalidArgumentException('Tag parent id must be positive.');
        }
    }

    public function value(): int
    {
        return $this->value;
    }
}