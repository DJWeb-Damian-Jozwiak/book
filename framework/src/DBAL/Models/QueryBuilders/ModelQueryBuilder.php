<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Models\QueryBuilders;

use DJWeb\Framework\DBAL\Contracts\Query\DeleteQueryBuilderContract;
use DJWeb\Framework\DBAL\Contracts\Query\InsertQueryBuilderContract;
use DJWeb\Framework\DBAL\Contracts\Query\QueryBuilderFacadeContract;
use DJWeb\Framework\DBAL\Contracts\Query\SelectQueryBuilderContract;
use DJWeb\Framework\DBAL\Contracts\Query\UpdateQueryBuilderContract;
use DJWeb\Framework\DBAL\Models\Model;
use DJWeb\Framework\DBAL\Query\Builders\QueryBuilder;

class ModelQueryBuilder
{
    public readonly QueryBuilderFacadeContract $facade;
    private SelectQueryBuilderContract
            |UpdateQueryBuilderContract
            |InsertQueryBuilderContract
            |DeleteQueryBuilderContract $builder;
    public function __construct(public readonly Model $model)
    {
        $this->facade = new QueryBuilder();
    }

    /**
     * @param string $name
     * @param array<int|string, mixed> $arguments
     *
     * @return $this
     */
    public function __call(string $name, array $arguments): static
    {
        $this->builder->$name(...$arguments);
        return $this;
    }

    /**
     * @param array<int, string> $columns
     *
     * @return $this
     */

    public function select(array $columns = ['*']): static
    {
        $builder = $this->facade->select($this->model->table);
        $builder->select($columns);
        $this->builder = $builder;
        return $this;
    }

    public function first(): ?Model
    {
        $builder = $this->facade->select($this->model->table);
        $result = $builder->first();
        $this->builder = $builder;
        return $result ? $this->hydrate($result) : null;
    }

    /**
     * @return array<int, Model>
     */
    public function get(): array
    {
        $results = $this->builder->get();
        return $this->hydrateMany($results);
    }

    /**
     * @param array<string, mixed> $attributes
     *
     * @return Model
     */
    protected function hydrate(array $attributes): Model
    {
        return $this->model->fill($attributes);
    }

    /**
     * @param array<int, mixed> $results
     *
     * @return array<int, Model>
     */
    protected function hydrateMany(array $results): array
    {
        return array_map(fn (array $result) => $this->hydrate($result), $results);
    }
}
