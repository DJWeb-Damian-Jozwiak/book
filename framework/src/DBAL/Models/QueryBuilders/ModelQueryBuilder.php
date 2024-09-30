<?php

namespace DJWeb\Framework\DBAL\Models\QueryBuilders;

use DJWeb\Framework\DBAL\Contracts\Query\QueryBuilderFacadeContract;
use DJWeb\Framework\DBAL\Models\Model;
use DJWeb\Framework\DBAL\Query\Builders\QueryBuilder;

class ModelQueryBuilder
{
    public readonly QueryBuilderFacadeContract $builder;
    public function __construct(protected(set) Model $model)
    {
        $this->builder = new QueryBuilder();
    }

    public function select($columns = ['*']): self
    {
        $this->builder->select($this->model->table)->select($columns);
        return $this;
    }

    public function insert(array $values): self
    {
        $this->builder->insert($this->model->table)->values($values);
        return $this;
    }

    public function update(array $values): self
    {
        $this->builder->update($this->model->table)->set($values);
        return $this;
    }

    public function first()
    {
        $result = $this->builder->select($this->model->table)->first();
        return $result ? $this->hydrate($result) : null;
    }

    public function get()
    {
        $results = $this->builder->select($this->model->table)->get();
        return $this->hydrateMany($results);
    }

    protected function hydrate(array $attributes)
    {
        return $this->model->fill($attributes);
    }

    protected function hydrateMany(array $results)
    {
        return array_map(fn($result) => $this->hydrate($result), $results);
    }
}