<?php

declare(strict_types=1);

namespace Modules\Post\Domain\ValueObjects;

use InvalidArgumentException;

final class PostId
{
    public function __construct(private readonly int $value)
    {
        if ($value <= 0) {
            throw new InvalidArgumentException('Id must be positive.');
        }
    }

    public function value(): int
    {
        return $this->value;
    }
}
