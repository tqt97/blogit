<?php

namespace Modules\Categories\Domain\Enums;

enum CategoryStatus: int
{
    case ACTIVE = 1;
    case INACTIVE = 0;

    public static function fromBool(bool $value): self
    {
        return $value ? self::ACTIVE : self::INACTIVE;
    }

    public function value(): bool
    {
        return $this === self::ACTIVE;
    }

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
        };
    }
}
