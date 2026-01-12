<?php

declare(strict_types=1);

namespace Modules\Post\Domain\ValueObjects;

use InvalidArgumentException;

final class PostCommentCount
{
    public function __construct(private readonly int $value)
    {
        if ($value < 0) {
            throw new InvalidArgumentException('Comment count cannot be less than 0.');
        }
    }

    public function value(): int
    {
        return $this->value;
    }
}
