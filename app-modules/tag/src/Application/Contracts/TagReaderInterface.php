<?php

namespace Modules\Tag\Application\Contracts;

interface TagReaderInterface
{
    /**
     * @return array<int, int>
     */
    public function filterExistingIds(array $ids): array;

    /**
     * @return array<int, array{id:int,label:string}>
     */
    public function listForSelect(): array;
}
