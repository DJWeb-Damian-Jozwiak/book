<?php

namespace DJWeb\Framework\DBAL\Models\QueryBuilders;

use DJWeb\Framework\DBAL\Contracts\Query\QueryBuilderFacadeContract;
use DJWeb\Framework\DBAL\Models\Model;
use DJWeb\Framework\DBAL\Query\Builders\DeleteQueryBuilder;
use DJWeb\Framework\DBAL\Query\Builders\InsertQueryBuilder;
use DJWeb\Framework\DBAL\Query\Builders\QueryBuilder;
use DJWeb\Framework\DBAL\Query\Builders\SelectQueryBuilder;
use DJWeb\Framework\DBAL\Query\Builders\UpdateQueryBuilder;

class ModelQueryBuilder
{
    public readonly QueryBuilderFacadeContract $facade;
    private SelectQueryBuilder|UpdateQueryBuilder|InsertQueryBuilder|DeleteQueryBuilder $builder;
    public function __construct(protected(set) Model $model)
    {
        $this->facade = new QueryBuilder();
    }

    public function select($columns = ['*']): static
    {
        $this->builder = $this->facade->select($this->model->table);
        $this->builder->select($columns);
        return $this;
    }

    public function first(): ?Model
    {
        $result = $this->builder->first();
        return $result ? $this->hydrate($result) : null;
    }

    public function get(): array
    {
        $results = $this->builder->get();
        return $this->hydrateMany($results);
    }

    /**
     * @param string $name
     * @param array<int|string, mixed> $arguments
     * @return $this
     */
    public function __call(string $name, array $arguments): static
    {
        $this->builder->$name(...$arguments);
        return $this;
    }

    protected function hydrate(array $attributes): Model
    {
        return $this->model->fill($attributes);
    }

    /**
     * @param array<int, mixed> $results
     * @return array<int, Model>
     */
    protected function hydrateMany(array $results): array
    {
        return array_map(fn(array $result) => $this->hydrate($result), $results);
    }
}