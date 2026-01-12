<?php

declare(strict_types=1);

namespace Modules\Tag\Application\Queries;

final readonly class ShowTagQuery
{
    public function __construct(public int $id) {}
}
