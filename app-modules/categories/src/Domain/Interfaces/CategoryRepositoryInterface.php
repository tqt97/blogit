    <?php

namespace Modules\Categories\Domain\Interfaces;

use Modules\Categories\Domain\Entities\Category;
use Modules\Category\Domain\ValueObjects\CategoryId;
use Modules\Category\Domain\ValueObjects\CategorySlug;

interface CategoryRepositoryInterface
{
    public function save(Category $category): void;

    // public function getById(CategoryId $id): ?Category;

    // public function existsBySlug(CategorySlug $slug, ?CategoryId $ignoreId = null): bool;

    // public function delete(CategoryId $id): void;
}
