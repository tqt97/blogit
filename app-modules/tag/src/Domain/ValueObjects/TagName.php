<?php

declare(strict_types=1);

namespace Modules\Tag\Domain\ValueObjects;

use InvalidArgumentException;

final class TagName
{
    public const MAX_LENGTH = 255;

    public function __construct(private string $value)
    {
        $value = trim($value);
        if ($value == '') {
            throw new InvalidArgumentException('Tag name cannot be empty.');
        }
        if (mb_strlen($value) > self::MAX_LENGTH) {
            throw new InvalidArgumentException('TagName too long.');
        }

        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }
}
