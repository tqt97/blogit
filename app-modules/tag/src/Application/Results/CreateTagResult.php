<?php

declare(strict_types=1);

namespace Modules\Tag\Application\Results;

use Modules\Tag\Domain\ValueObjects\TagId;

final readonly class CreateTagResult
{
    public function __construct(
        public TagId $id,
    ) {}
}
