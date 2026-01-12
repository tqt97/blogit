<?php

declare(strict_types=1);

namespace Modules\Tag\Domain\ValueObjects;

enum SortDirection: string
{
    case Asc = 'asc';
    case Desc = 'desc';

    public static function fromString(?string $value, self $default = self::Desc): self
    {
        if (! is_string($value)) {
            return $default;
        }

        $value = strtolower(trim($value));

        return match ($value) {
            'asc' => self::Asc,
            'desc' => self::Desc,
            default => $default,
        };
    }
}
