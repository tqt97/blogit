<?php

declare(strict_types=1);

namespace Modules\Tag\Application\Results;

use Modules\Tag\Domain\ValueObjects\TagId;

final readonly class UpdateTagResult
{
    public function __construct(
        public TagId $id,
    ) {}
}
