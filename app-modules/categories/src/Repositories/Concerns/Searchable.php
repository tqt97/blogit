<?php

namespace Modules\Categories\Repositories\Concerns;

use Illuminate\Database\Eloquent\Model;

trait Searchable
{
    /**
     * The model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Find a record by a specific field and value.
     *
     * @param string $field
     * @param mixed $value
     * @param array $with
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function findBy(string $field, $value, array $with = []): ?Model
    {
        return $this->model->with($with)->where($field, $value)->first();
    }
}