<?php

declare(strict_types=1);

namespace Modules\Post\Domain\ValueObjects;

use InvalidArgumentException;

final class PostTitle
{
    public function __construct(private readonly string $value)
    {
        if (empty($value)) {
            throw new InvalidArgumentException('Title cannot be empty.');
        }

        if (mb_strlen($value) > 255) {
            throw new InvalidArgumentException('Title cannot be longer than 255 characters.');
        }

        if (mb_strlen($value) < 3) {
            throw new InvalidArgumentException('Title cannot be shorter than 3 characters.');
        }
    }

    public function value(): string
    {
        return $this->value;
    }
}
