<?php

namespace Modules\Category\Domain\Enums;

enum CategoryStatus: string
{
    case ACTIVE = true;
    case INACTIVE = false;

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
        };
    }
}
