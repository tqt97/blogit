<?php

declare(strict_types=1);

namespace Modules\Tag\Domain\ValueObjects;

final readonly class SearchTerm
{
    public function __construct(public string $value)
    {
        $v = trim($value);

        if ($v === '') {
            throw new \InvalidArgumentException('Search term cannot be empty.');
        }

        if (mb_strlen($v) > 255) {
            throw new \InvalidArgumentException('Search term too long.');
        }
    }

    public static function fromNullable(?string $value): ?self
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : new self($value);
    }
}
