<?php

namespace Modules\Shared\Contracts\Taxonomy;

interface CategoryQuery
{
    public function listForSelect(): array;
}
