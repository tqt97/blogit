<?php

declare(strict_types=1);

namespace Modules\Post\Domain\ValueObjects;

use InvalidArgumentException;

final class PostUserId
{
    public function __construct(private readonly int $value)
    {
        if ($value < 1) {
            throw new InvalidArgumentException('User ID cannot be less than 1.');
        }
    }

    public function value(): int
    {
        return $this->value;
    }
}
