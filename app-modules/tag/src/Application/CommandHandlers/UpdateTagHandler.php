<?php

declare(strict_types=1);

namespace Modules\Tag\Application\CommandHandlers;

use Illuminate\Support\Str;
use Modules\Tag\Application\Commands\UpdateTagCommand;
use Modules\Tag\Application\DTOs\TagDTO;
use Modules\Tag\Domain\Repositories\TagRepository;
use Modules\Tag\Domain\Rules\UniqueTagSlugRule;
use Modules\Tag\Domain\ValueObjects\TagId;
use Modules\Tag\Domain\ValueObjects\TagName;
use Modules\Tag\Domain\ValueObjects\TagSlug;
use RuntimeException;

final class UpdateTagHandler
{
    public function __construct(
        private readonly TagRepository $repo,
        private readonly UniqueTagSlugRule $uniqueRule,
    ) {}

    public function handle(UpdateTagCommand $cmd): TagDTO
    {
        $id = new TagId($cmd->id);

        $tag = $this->repo->getById($id);
        if (! $tag) {
            throw new RuntimeException('Tag not found.');
        }

        $name = new TagName($cmd->name);
        $slug = new TagSlug($cmd->slug ?: Str::slug($cmd->name));

        $this->uniqueRule->ensureUnique($slug, $id);

        $tag->rename($name, $slug);
        $this->repo->save($tag);

        return TagDTO::fromEntity($tag);
    }
}
