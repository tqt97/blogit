<?php

declare(strict_types=1);

namespace Modules\Post\Domain\ValueObjects;

enum PostStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Pending = 'pending';

    public static function fromString(?string $value, self $default = self::Draft): self
    {
        if (! is_string($value)) {
            return $default;
        }

        return self::tryFrom(trim($value)) ?? $default;
    }
}
