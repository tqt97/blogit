<?php

declare(strict_types=1);

namespace Modules\Post\Domain\ValueObjects;

use InvalidArgumentException;

final class PostContent
{
    public function __construct(private readonly string $value)
    {
        if (empty($value)) {
            throw new InvalidArgumentException('Content cannot be empty.');
        }
    }

    public function value(): string
    {
        return $this->value;
    }
}
