<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Models\Decorators;

use DJWeb\Framework\DBAL\Contracts\Query\QueryBuilderFacadeContract;
use DJWeb\Framework\DBAL\Models\Model;
use DJWeb\Framework\DBAL\Models\PropertyObserver;

class EntityInserter
{
    private QueryBuilderFacadeContract $query_builder;
    private PropertyObserver $property_watcher;

    public function __construct(
        private Model $model
    ) {
        $this->query_builder =
            $this->model->query_builder->facade;
        $this->property_watcher = $this->model->observer;
    }

    public function insert(): ?string
    {
        $builder = $this->query_builder->insert($this->model->table);
        $builder->values($this->property_watcher->getChangedProperties())
            ->execute();

        return $builder->getInsertId();
    }
}
