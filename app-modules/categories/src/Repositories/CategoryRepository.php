<?php

namespace Modules\Categories\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Categories\Models\Category;

class CategoryRepository
{
    /**
     * The Category model instance.
     *
     * @var Category
     */
    protected Category $model;

    /**
     * CategoryRepository constructor.
     *
     * @param Category $model
     */
    public function __construct(Category $model)
    {
        $this->model = $model;
    }

    /**
     * Get all category by params
     *
     * @param $onlyRoot
     * @return LengthAwarePaginator
     */
    public function getAll($onlyRoot = false): LengthAwarePaginator
    {
        $query = $this->model->with('childrenRecursive');

        if ($onlyRoot) {
            $query->whereNull('parent_id');
        }
        return $query->orderBy('id', 'desc')->paginate(10);
    }

    /**
     * Find Category by ID
     *
     * @param int $id
     * @return Category|null
     */
    public function find(int $id): ?Category
    {
        return $this->model->find($id);
    }

    /**
     * Create new Category
     *
     * @param array<string, mixed> $data
     * @return Category
     */
    public function create(array $data): Category
    {
        return $this->model->create($data);
    }

    /**
     * Update category by ID
     *
     * @param int   $id
     * @param array<string, mixed> $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $category = $this->model->findOrFail($id);
        return $category->update($data);
    }

    /**
     * Delete category by ID
     *
     * @param int $id
     * @return bool|null
     *
     * @throws \Exception
     */
    public function delete(int $id): ?bool
    {
        $category = $this->model->findOrFail($id);
        $category->children()->delete();
        return $category->delete();
    }
}