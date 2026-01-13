<?php

declare(strict_types=1);

namespace Modules\Post\Application\CommandHandlers;

use Modules\Post\Application\Commands\IncrementPostViewsCountCommand;
use Modules\Post\Domain\Repositories\PostRepository;
use Modules\Post\Domain\ValueObjects\PostId;

final class IncrementPostViewsCountHandler
{
    public function __construct(private readonly PostRepository $repository) {}

    public function handle(IncrementPostViewsCountCommand $command): void
    {
        $this->repository->incrementViews(new PostId($command->id));
    }
}
