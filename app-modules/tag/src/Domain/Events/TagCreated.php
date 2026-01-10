<?php

declare(strict_types=1);

namespace Modules\Tag\Domain\Events;

use Modules\Tag\Domain\Entities\Tag;

final readonly class TagCreated
{
    public function __construct(public Tag $tag) {}
}
