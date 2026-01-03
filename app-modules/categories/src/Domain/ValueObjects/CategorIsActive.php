<?php

declare(strict_types=1);

namespace Modules\Categories\Domain\ValueObjects;


final class CategoryIsActive
{
    private function __construct(
        private readonly bool $value
    ) {}

    public static function active(): self
    {
        return new self(true);
    }

    public static function inactive(): self
    {
        return new self(false);
    }

    public function value(): bool
    {
        return $this->value;
    }
}