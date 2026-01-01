<?php

namespace Modules\Shared\Contracts\Taxonomy;

interface TagQuery
{
    /**
     * @return array<int, array{id:int,label:string}>
     */
    public function listForSelect(): array;
}
