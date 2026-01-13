<?php

declare(strict_types=1);

namespace Modules\Categories\Application\CommandHandlers;

use Modules\Categories\Application\Commands\CreateCategoryCommand;
use Modules\Categories\Application\Port\EventBus\EventBus;
use Modules\Categories\Application\Port\Responses\CreateCategoryResponse;
use Modules\Categories\Application\Port\Transaction\TransactionManager;
use Modules\Categories\Domain\Entities\CategoryEntity;
use Modules\Categories\Domain\Interfaces\CategoryRepositoryInterface;
use Modules\Categories\Domain\ValueObjects\CategoryDescription;
use Modules\Categories\Domain\ValueObjects\CategoryId;
use Modules\Categories\Domain\ValueObjects\CategoryIsActive;
use Modules\Categories\Domain\ValueObjects\CategoryName;
use Modules\Categories\Domain\ValueObjects\CategoryParentId;

final class CreateCategoryHandler
{
    public function __construct(
        private readonly CategoryRepositoryInterface $repository,
        private readonly TransactionManager $transactionManager,
        private readonly EventBus $eventBus,
    ) {}

    public function handle(CreateCategoryCommand $command): CreateCategoryResponse
    {
        return $this->transactionManager->withinTransaction(function () use ($command) {
            $data = CategoryEntity::create(
                new CategoryName($command->name),
                null,
                $command->description ? new CategoryDescription($command->description) : null,
                $command->parent_id ? new CategoryParentId($command->parent_id) : null,
                CategoryIsActive::from($command->is_active)
            );

            $category = $this->repository->save($data);

            $this->eventBus->publish($category->pullEvents());

            return new CreateCategoryResponse(new CategoryId($category->id()->value()));
        });
    }
}
