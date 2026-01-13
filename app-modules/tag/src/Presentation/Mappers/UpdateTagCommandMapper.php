<?php

declare(strict_types=1);

namespace Modules\Tag\Presentation\Mappers;

use Modules\Tag\Application\Commands\UpdateTagCommand;

final class UpdateTagCommandMapper
{
    public function __invoke(int $id, array $data): UpdateTagCommand
    {
        return new UpdateTagCommand($id, $data['name'], $data['slug']);
    }
}
