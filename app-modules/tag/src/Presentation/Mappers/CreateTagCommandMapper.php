<?php

declare(strict_types=1);

namespace Modules\Tag\Presentation\Mappers;

use Modules\Tag\Application\Commands\CreateTagCommand;

final class CreateTagCommandMapper
{
    public function __invoke(array $data): CreateTagCommand
    {
        return new CreateTagCommand($data['name'], $data['slug']);
    }
}
