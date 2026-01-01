<?php
declare(strict_types=1);

namespace Modules\Tag\Application\QueryHandlers;

use Modules\Tag\Application\DTOs\TagDTO;
use Modules\Tag\Application\Queries\ShowTagQuery;
use Modules\Tag\Infrastructure\Persistence\Eloquent\Models\TagModel;

final class ShowTagHandler
{
    public function handle(ShowTagQuery $q): ?TagDTO
    {
        $m = TagModel::query()->select(['id','name','slug'])->find($q->id);
        return $m ? new TagDTO((int)$m->id, (string)$m->name, (string)$m->slug) : null;
    }
}
