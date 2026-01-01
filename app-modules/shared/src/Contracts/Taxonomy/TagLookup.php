<?php

namespace Modules\Shared\Contracts\Taxonomy;

interface TagLookup
{
    /**
     * @return int[] valid IDs (unique)
     */
    public function filterExistingIds(array $ids): array;
}
