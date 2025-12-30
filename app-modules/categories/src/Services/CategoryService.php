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
     * @param bool $onlyRoot
     * @return LengthAwarePaginator
     */
    public function getAll($onlyRoot = false): LengthAwarePaginator
    {
        return $this->categoryRepository->getAll($onlyRoot);
    }

    /**
     * Store a new category
     *
     * @param array $data
     * @return void
     */
    public function store(array $data): void
    {
        $this->categoryRepository->create($data);
    }

    /**
     * Update an existing category
     *
     * @param int $id
     * @param array $data
     * @return void
     */
    public function update(int $id, array $data): void
    {
        $this->categoryRepository->update($id, $data);
    }

    /**
     * Delete an existing category
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {
        $this->categoryRepository->delete($id);
    }
}