<?php

declare(strict_types=1);

namespace Modules\Categories\Domain\ValueObjects;

use InvalidArgumentException;

final class CategoryId
{
    public function __construct(private int $value)
    {
        if ($value <= 0) {
            throw new InvalidArgumentException('TagId must be positive.');
        }
    }

    public function value(): int
    {
        return $this->value;
    }
}