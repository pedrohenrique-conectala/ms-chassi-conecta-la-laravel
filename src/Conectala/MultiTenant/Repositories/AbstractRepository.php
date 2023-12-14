<?php

namespace Conectala\MultiTenant\Repositories;

class AbstractRepository
{
    protected mixed $model;

    public function __construct()
    {
        $this->model = $this->resolveModel();
    }

    protected function resolveModel()
    {
        return app($this->model);
    }

    /**
     * Find a model by its primary key.
     *
     * @param   mixed       $id
     * @param   array       $columns
     * @return  object|null
     */
    public function find(mixed $id, array $columns = ['*']): ?object
    {
        return $this->model->find($id, $columns);
    }

    /**
     * Set a given attribute on the model.
     *
     * @param   string $key
     * @param   mixed  $value
     * @return  mixed
     */
    public function setAttribute(string $key, mixed $value): mixed
    {
        return $this->model->setAttribute($key, $value);
    }

    /**
     * Get an attribute from the model.
     *
     * @param string $key
     * @return mixed
     */
    public function getAttribute(string $key): mixed
    {
        return $this->model->getAttribute($key);
    }

    /**
     * Save the model to the database.
     *
     * @param  array  $options
     * @return bool
     */
    public function save(array $options = []): bool
    {
        return $this->model->save($options);
    }

    /**
     * Insert new records into the database.
     *
     * @param  array  $values
     * @return bool
     */
    public function create(array $values): bool
    {
        return $this->model->create($values);
    }

    /**
     * Recupera o registro pelo ID.
     *
     * @param   int         $id
     * @return  object|null
     */
    public function getById(int $id): ?object
    {
        return $this->model->getById($id);
    }

    /**
     * Recupera o registro pelo campo informado.
     *
     * @param   string  $field
     * @param   string  $operation
     * @param   string  $value
     * @param   array   $select
     * @return object|null
     */
    public function getByReference(string $field, string $operation, string $value, array $select = ['*']): ?object
    {
        return $this->model->select($select)->where($field, $operation, $value)->first();
    }
}
