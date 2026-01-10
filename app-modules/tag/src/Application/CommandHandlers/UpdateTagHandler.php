<?php

declare(strict_types=1);

namespace Modules\Tag\Application\CommandHandlers;

use Modules\Tag\Application\Commands\UpdateTagCommand;
use Modules\Tag\Domain\Entities\Tag;
use Modules\Tag\Domain\Exceptions\TagNotFoundException;
use Modules\Tag\Domain\Repositories\TagRepository;
use Modules\Tag\Domain\ValueObjects\TagId;
use Modules\Tag\Domain\ValueObjects\TagName;
use Modules\Tag\Domain\ValueObjects\TagSlug;

final class UpdateTagHandler
{
    public function __construct(
        private readonly TagRepository $repository,
    ) {}

    public function handle(UpdateTagCommand $command): Tag
    {
        $tag = $this->repository->getById(new TagId($command->id));
        if (! $tag) {
            throw new TagNotFoundException;
        }

        $tag->rename(new TagName($command->name));
        $tag->changeSlug(new TagSlug($command->slug));

        return $this->repository->save($tag);
    }
}
