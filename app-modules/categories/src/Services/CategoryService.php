<?php

namespace Modules\Categories\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Categories\Repositories\CategoryRepository;

class CategoryService
{
    protected $categoryRepository;

    /**
     * Inject Category repository
     *
     * @param CategoryRepository
     */
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Get all category with params
     *
     * @param  bool  $onlyRoot
     */
    public function getAll($onlyRoot = false): LengthAwarePaginator
    {
        return $this->categoryRepository->getAll($onlyRoot);
    }

    /**
     * Store a new category
     */
    public function store(array $data): void
    {
        $this->categoryRepository->create($data);
    }

    /**
     * Update an existing category
     */
    public function update(int $id, array $data): void
    {
        $this->categoryRepository->update($id, $data);
    }

    /**
     * Delete an existing category
     */
    public function delete(int $id): void
    {
        $this->categoryRepository->delete($id);
    }
}
