<?php

declare(strict_types=1);

namespace Modules\Categories\Application\Responses;

use Modules\Categories\Domain\ValueObjects\CategoryId;

final readonly class UpdateCategoryResponse
{
    public function __construct(
        public CategoryId $id,
    ) {}
}
