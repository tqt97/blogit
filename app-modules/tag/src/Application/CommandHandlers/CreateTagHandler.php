<?php

namespace Modules\Tag\Application\CommandHandlers;

use Modules\Tag\Application\Commands\CreateTagCommand;
use Modules\Tag\Application\DTOs\TagDTO;
use Modules\Tag\Domain\Repositories\TagRepositoryInterface;
use Modules\Tag\Domain\Rules\UniqueTagSlugRule;
use Modules\Tag\Domain\ValueObjects\TagName;
use Modules\Tag\Domain\ValueObjects\TagSlug;
use Modules\Tag\Infrastructure\Persistence\Eloquent\Models\Tag;

class CreateTagHandler
{
    public function __construct(
        private readonly TagRepositoryInterface $repo,
        private readonly UniqueTagSlugRule $uniqueSlugRule,
    ) {}

    public function handle(CreateTagCommand $command): TagDTO
    {
        $name = new TagName($command->name);
        $slug = new TagSlug($command->slug);

        $this->uniqueSlugRule->ensureUnique($slug);

        $tag = Tag::create([
            'name' => $name->value(),
            'slug' => $slug->value(),
        ]);

        $this->repo->save($tag);

        return TagDTO::fromEntity($tag);
    }
}
