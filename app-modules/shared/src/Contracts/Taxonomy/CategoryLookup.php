<?php

namespace Modules\Shared\Contracts\Taxonomy;

interface CategoryLookup
{
    public function exists(int $id): bool;

    /** @throws \Illuminate\Validation\ValidationException */
    public function assertExists(int $id): void;
}
