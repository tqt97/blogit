<?php

declare(strict_types=1);

namespace Modules\Tag\Application\CommandHandlers;

use Modules\Tag\Application\Commands\UpdateTagCommand;
use Modules\Tag\Domain\Entities\Tag;
use Modules\Tag\Domain\Repositories\TagRepository;
use Modules\Tag\Domain\Rules\UniqueTagSlugRule;
use Modules\Tag\Domain\ValueObjects\TagId;
use Modules\Tag\Domain\ValueObjects\TagName;
use Modules\Tag\Domain\ValueObjects\TagSlug;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class UpdateTagHandler
{
    public function __construct(
        private readonly TagRepository $repo,
        private readonly UniqueTagSlugRule $uniqueSlugRule,
    ) {}

    public function handle(UpdateTagCommand $cmd): Tag
    {
        $id = new TagId($cmd->id);
        $tag = $this->repo->getById(new TagId($id->value()));
        if (! $tag) {
            throw new NotFoundHttpException('Tag not found.');
        }

        $name = new TagName($cmd->name);
        $slug = new TagSlug($cmd->slug);
        if (! $this->uniqueSlugRule->isUnique($slug, $id)) {
            throw new \DomainException('Slug already exists.');
        }

        $tag->rename($name);
        $tag->changeSlug($slug);

        return $this->repo->save($tag);
    }
}
