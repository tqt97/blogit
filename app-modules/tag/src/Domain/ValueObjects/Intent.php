<?php

declare(strict_types=1);

namespace Modules\Tag\Domain\ValueObjects;

enum Intent: string
{
    case Default = 'default';
    case CreateAndContinue = 'create_and_continue';

    public static function fromString(?string $value, self $default = self::Default): self
    {
        if (! is_string($value)) {
            return $default;
        }

        return self::tryFrom(trim($value)) ?? $default;
    }
}
