<?php

declare(strict_types=1);

namespace Modules\Tag\Application\Commands;

final class DeleteTagCommand
{
    public function __construct(public readonly int $id) {}
}
