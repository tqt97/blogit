<?php

declare(strict_types=1);

namespace Modules\Tag\Application\CommandHandlers;

use Modules\Tag\Application\Commands\CreateTagCommand;
use Modules\Tag\Domain\Entities\Tag;
use Modules\Tag\Domain\Repositories\TagRepository;
use Modules\Tag\Domain\ValueObjects\TagName;
use Modules\Tag\Domain\ValueObjects\TagSlug;

final class CreateTagHandler
{
    public function __construct(
        private readonly TagRepository $repository,
    ) {}

    public function handle(CreateTagCommand $command): Tag
    {
        $tag = Tag::create(new TagName($command->name), new TagSlug($command->slug));

        return $this->repository->save($tag);
    }
}
