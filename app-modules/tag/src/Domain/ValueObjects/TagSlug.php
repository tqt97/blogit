<?php

declare(strict_types=1);

namespace Modules\Tag\Domain\ValueObjects;

use InvalidArgumentException;

final class TagSlug
{
    public const MAX_LENGTH = 255;

    public const REGEX = '/^[a-z0-9]+(?:-[a-z0-9]+)*$/';

    public function __construct(private string $value)
    {
        $value = trim($value);
        if ($value === '') {
            throw new InvalidArgumentException('Tag slug cannot be empty.');
        }
        if (! preg_match(self::REGEX, $value)) {
            throw new InvalidArgumentException('Tag slug format is invalid.');
        }

        if (strlen($value) > self::MAX_LENGTH) {
            throw new InvalidArgumentException('Tag slug is too long.');
        }
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }
}
