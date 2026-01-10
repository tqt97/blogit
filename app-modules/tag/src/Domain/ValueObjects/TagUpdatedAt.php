<?php

declare(strict_types=1);

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
