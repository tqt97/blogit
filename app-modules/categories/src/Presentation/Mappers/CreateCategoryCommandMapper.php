<?php

declare(strict_types=1);

namespace Modules\Categories\Presentation\Mappers;

use Modules\Categories\Application\Commands\CreateCategoryCommand;

final class CreateCategoryCommandMapper
{
    public function __invoke(array $data): CreateCategoryCommand
    {
        return new CreateCategoryCommand($data['name'], null, $data['parent_id'], $data['description']);
    }
}
