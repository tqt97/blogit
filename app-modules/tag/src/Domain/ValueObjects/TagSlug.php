<?php

declare(strict_types=1);

namespace Modules\Tag\Domain\ValueObjects;

use InvalidArgumentException;

final class TagSlug
{
    public function __construct(private string $value)
    {
        $value = trim($value);
        if ($value === '') {
            throw new InvalidArgumentException('Tag slug cannot be empty.');
        }
        if (! preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $value)) {
            throw new InvalidArgumentException('Tag slug format is invalid.');
        }

        if (strlen($value) > 255) {
            throw new InvalidArgumentException('Tag slug is too long.');
        }
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }
}
