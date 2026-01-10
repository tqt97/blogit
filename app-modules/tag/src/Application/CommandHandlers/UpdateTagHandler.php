<?php

declare(strict_types=1);

namespace Modules\Tag\Application\CommandHandlers;

use Modules\Tag\Application\Commands\UpdateTagCommand;
use Modules\Tag\Domain\Entities\Tag;
use Modules\Tag\Domain\Exceptions\SlugAlreadyExistsException;
use Modules\Tag\Domain\Exceptions\TagNotFoundException;
use Modules\Tag\Domain\Repositories\TagRepository;
use Modules\Tag\Domain\Services\TagSlugUniquenessChecker;
use Modules\Tag\Domain\ValueObjects\TagId;
use Modules\Tag\Domain\ValueObjects\TagName;
use Modules\Tag\Domain\ValueObjects\TagSlug;

final class UpdateTagHandler
{
    public function __construct(
        private readonly TagRepository $repository,
        private readonly TagSlugUniquenessChecker $uniqueSlugRule,
    ) {}

    public function handle(UpdateTagCommand $command): Tag
    {
        $id = new TagId($command->id);
        $tag = $this->repository->getById($id);
        if (! $tag) {
            throw new TagNotFoundException;
        }

        $name = new TagName($command->name);
        $slug = new TagSlug($command->slug);
        if (! $this->uniqueSlugRule->isUnique($slug, $id)) {
            throw new SlugAlreadyExistsException;
        }

        $tag->rename($name);
        $tag->changeSlug($slug);

        return $this->repository->save($tag);
    }
}
