<?php

declare(strict_types=1);

namespace Modules\Tag\Domain\Events;

use Modules\Tag\Domain\Entities\Tag;

final class TagCreated
{
    public function __construct(public readonly Tag $tag) {}
}
