<?php

declare(strict_types=1);

namespace Modules\Categories\Domain\ValueObjects;

enum SortField: string
{
    case Id = 'id';
    case Name = 'name';
    case Slug = 'slug';
    case ParentId = 'parent_id';
    case IsActive = 'is_active';
    case CreatedAt = 'created_at';
    case UpdatedAt = 'updated_at';

    public static function fromString(?string $value, self $default = self::Id): self
    {
        if (! is_string($value)) {
            return $default;
        }

        $value = trim($value);

        return self::tryFrom($value) ?? $default;
    }
}
