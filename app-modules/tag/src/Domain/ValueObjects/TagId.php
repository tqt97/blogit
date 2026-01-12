<?php

declare(strict_types=1);

namespace Modules\Tag\Domain\ValueObjects;

use InvalidArgumentException;

final class TagId
{
    public function __construct(private readonly int $value)
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
