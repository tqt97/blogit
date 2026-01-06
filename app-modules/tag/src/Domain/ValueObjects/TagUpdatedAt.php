<?php

namespace Modules\Tag\Domain\ValueObjects;

use DateTimeImmutable;

final class TagUpdatedAt
{
    public function __construct(private readonly DateTimeImmutable $value) {}

    public function value(): DateTimeImmutable
    {
        return $this->value;
    }
}
