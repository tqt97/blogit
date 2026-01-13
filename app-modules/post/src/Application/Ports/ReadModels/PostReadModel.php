<?php

declare(strict_types=1);

namespace Modules\Post\Application\Ports\ReadModels;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Post\Application\DTOs\PostDTO;
use Modules\Post\Domain\ValueObjects\Pagination;
use Modules\Post\Domain\ValueObjects\SearchTerm;
use Modules\Post\Domain\ValueObjects\Sorting;
use Modules\Post\Domain\ValueObjects\TrashedFilter;

interface PostReadModel
{
    /**
     * Paginate posts based on search criteria and sorting.
     */
    public function paginate(
        ?SearchTerm $search,
        Pagination $pagination,
        Sorting $sorting,
        TrashedFilter $trashed,
        ?int $categoryId = null,
        ?int $tagId = null,
        ?int $authorId = null
    ): LengthAwarePaginator;

    /**
     * Find a post DTO by its ID.
     */
    public function find(int $id): ?PostDTO;

    /**
     * Find a post DTO by its slug.
     */
    public function findBySlug(string $slug): ?PostDTO;

    /**
     * Get related posts (excluding the given post ID).
     *
     * @return PostDTO[]
     */
    public function getRelated(int $postId, int $limit = 4): array;
}
