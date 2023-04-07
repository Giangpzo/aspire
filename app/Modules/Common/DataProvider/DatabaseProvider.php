<?php

namespace App\Modules\Common\DataProvider;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\HigherOrderTapProxy;
use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class DatabaseProvider
{
    public $model;

    protected $modelInstance;

    /**
     * Create record
     *
     * @param $attributes
     * @return Model
     */
    public function create($attributes)
    {
        return $this->newQuery()->create($attributes);
    }

    /**
     * @param null $query
     * @return mixed|null
     */
    public function newQuery($query = null)
    {
        if (empty($this->model)) {
            throw new InvalidArgumentException('Model Class was missing in Provider');
        }

        # Start from scratch if there is no initial query
        if (is_null($query)) {
            $query = (new $this->model)->newQuery();
        }

        return $query;
    }

    /**
     * Return model instance to access instance's method
     * @return Model
     */
    public function getInstance()
    {
        if (is_null($this->modelInstance)) {
            $this->modelInstance = new $this->model;
        }

        return $this->modelInstance;
    }

    /**
     * Get model by id
     *
     * @param $id
     * @return null
     */
    public function getById($id)
    {
        if (!$id instanceof Model) {
            return $this->newQuery()->find($id);
        }

        return null;
    }

    /**
     * Paginate result
     *
     * @param $query
     * @param bool $paginate
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function paginateResult($query, $paginate = true)
    {
        if ($paginate) {
            return $query->paginate(request()->get('per_page'));
        }

        return $query->get();
    }

    /**
     * Update
     * @param $id
     * @param $attributes
     * @return HigherOrderTapProxy|mixed
     */
    public function update($id, $attributes)
    {
        $originId = $id;
        if (!$id instanceof Model) {
            $id = $this->getById($id);
        }
        if (!$id) {
            throw (new ModelNotFoundException())->setModel($this->model, [$originId]);
        }

        return tap($id, function ($instance) use ($attributes) {
            return $instance->update($attributes);
        });
    }
}