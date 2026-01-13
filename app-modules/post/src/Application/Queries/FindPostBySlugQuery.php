<?php

declare(strict_types=1);

namespace Modules\Post\Application\Queries;

final readonly class FindPostBySlugQuery
{
    public function __construct(public string $slug) {}
}
