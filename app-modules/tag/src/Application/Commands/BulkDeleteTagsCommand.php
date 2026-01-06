<?php

namespace Modules\Tag\Application\Commands;

final class BulkDeleteTagsCommand
{
    public function __construct(public readonly array $ids) {}
}
