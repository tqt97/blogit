<?php declare(strict_types=1);

namespace Modules\Tag\Application\CommandHandlers;

use Modules\Tag\Application\Commands\CreateTagCommand;
use Modules\Tag\Application\DTOs\TagDTO;
use Modules\Tag\Domain\Entities\Tag;
use Modules\Tag\Domain\Repositories\TagRepository;
use Modules\Tag\Domain\Rules\UniqueTagSlugRule;
use Modules\Tag\Domain\ValueObjects\TagName;
use Modules\Tag\Domain\ValueObjects\TagSlug;

class CreateTagHandler
{
    public function __construct(
        private readonly TagRepository $repo,
        private readonly UniqueTagSlugRule $uniqueSlugRule,
    ) {}

    public function handle(CreateTagCommand $command): TagDTO
    {
        $name = new TagName($command->name);
        $slug = new TagSlug($command->slug);

        $this->uniqueSlugRule->ensureUnique($slug);

        $tag = Tag::create(
            name: $name,
            slug: $slug,
        );

        $this->repo->save($tag);

        return TagDTO::fromEntity($tag);
    }
}
