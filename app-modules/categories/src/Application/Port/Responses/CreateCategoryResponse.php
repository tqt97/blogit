<?php

declare(strict_types=1);

namespace Modules\Categories\Application\Port\Responses;

use Modules\Categories\Domain\ValueObjects\CategoryId;

final readonly class CreateCategoryResponse
{
    public function __construct(
        public CategoryId $id,
    ) {}
}
