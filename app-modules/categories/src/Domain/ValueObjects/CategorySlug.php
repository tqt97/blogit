<?php

declare(strict_types=1);

namespace Modules\Categories\Domain\ValueObjects;

use InvalidArgumentException;

final class CategorySlug
{
    public function __construct(private string $value)
    {
        $value = trim($value);
        if ($value === '') {
            throw new InvalidArgumentException('Tag slug cannot be empty.');
        }
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }
}