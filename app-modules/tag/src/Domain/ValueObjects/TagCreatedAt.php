<?php

declare(strict_types=1);

namespace Modules\Tag\Domain\ValueObjects;

final class TagCreatedAt
{
    public function __construct(private readonly string $value) {}

    public function value(): string
    {
        return $this->value;
    }
}
