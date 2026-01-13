<?php

declare(strict_types=1);

namespace Modules\Post\Domain\ValueObjects;

use InvalidArgumentException;

final class PostPublishedAt
{
    public function __construct(private readonly ?string $value)
    {
        if ($value !== null && ! $this->isValidDate($value)) {
            throw new InvalidArgumentException("Invalid published_at date format: {$value}. Expected ISO-8601.");
        }
    }

    public function value(): ?string
    {
        return $this->value;
    }

    private function isValidDate(string $date): bool
    {
        return (bool) preg_match('/^\d{4}-\d{2}-\d{2}/', $date);
    }
}
