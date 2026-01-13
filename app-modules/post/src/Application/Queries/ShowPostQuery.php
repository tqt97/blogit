<?php

declare(strict_types=1);

namespace Modules\Post\Application\Queries;

final readonly class ShowPostQuery
{
    public function __construct(public int $id) {}
}
