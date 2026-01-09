<?php

declare(strict_types=1);

namespace Modules\Tag\Application\CommandHandlers;

use Modules\Tag\Application\Commands\CreateTagCommand;
use Modules\Tag\Domain\Entities\Tag;
use Modules\Tag\Domain\Exceptions\SlugAlreadyExistsException;
use Modules\Tag\Domain\Repositories\TagRepository;
use Modules\Tag\Domain\Services\TagSlugUniquenessChecker;
use Modules\Tag\Domain\ValueObjects\TagName;
use Modules\Tag\Domain\ValueObjects\TagSlug;

class CreateTagHandler
{
    public function __construct(
        private readonly TagRepository $repository,
        private readonly TagSlugUniquenessChecker $uniqueSlugRule,
    ) {}

    public function handle(CreateTagCommand $command): Tag
    {
        $name = new TagName($command->name);
        $slug = new TagSlug($command->slug);

        if (! $this->uniqueSlugRule->isUnique($slug)) {
            throw new SlugAlreadyExistsException;
        }

        $tag = Tag::create($name, $slug);

        return $this->repository->save($tag);
    }
}
