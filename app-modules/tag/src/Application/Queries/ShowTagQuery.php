<?php

declare(strict_types=1);

namespace Modules\Tag\Application\Queries;

final class ShowTagQuery
{
    public function __construct(public readonly int $id) {}
}
