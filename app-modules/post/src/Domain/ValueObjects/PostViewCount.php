<?php

declare(strict_types=1);

namespace Modules\Post\Domain\ValueObjects;

use InvalidArgumentException;

final class PostViewCount
{
    public function __construct(private readonly int $value)
    {
        if ($value < 0) {
            throw new InvalidArgumentException('View count cannot be less than 0.');
        }
    }

    public function value(): int
    {
        return $this->value;
    }
}
