<?php

declare(strict_types=1);

namespace Modules\Post\Domain\ValueObjects;

final class PostPublishedAt
{
    public function __construct(private readonly ?string $value) {}

    public function value(): ?string
    {
        return $this->value;
    }
}
