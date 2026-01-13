<?php

declare(strict_types=1);

namespace Modules\Post\Domain\ValueObjects;

enum TrashedFilter: string
{
    case Without = 'without';
    case Only = 'only';
    case With = 'with';

    public static function fromString(?string $value, self $default = self::Without): self
    {
        if (! is_string($value)) {
            return $default;
        }

        return self::tryFrom(trim($value)) ?? $default;
    }
}
