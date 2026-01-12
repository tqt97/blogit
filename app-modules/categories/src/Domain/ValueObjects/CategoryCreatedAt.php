<?php

declare(strict_types=1);

namespace Modules\Categories\Domain\ValueObjects;

final class CategoryCreatedAt
{
    public function __construct(private string $value)
    {
        $value = trim($value);
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }
}
