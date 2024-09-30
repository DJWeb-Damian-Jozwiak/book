<?php

namespace DJWeb\Framework\DBAL\Models\Decorators;

use DJWeb\Framework\DBAL\Contracts\Query\QueryBuilderFacadeContract;
use DJWeb\Framework\DBAL\Models\Model;
use DJWeb\Framework\DBAL\Models\PropertyWatcher;

class EntityInserter
{
    private QueryBuilderFacadeContract $query_builder;
    private PropertyWatcher $property_watcher;

    public function __construct(

        private Model $model
    ) {
        $this->query_builder =
            $this->model->query_builder->facade;
        $this->property_watcher = $this->model->watcher;
    }

    public function insert(): ?string
    {
        $builder = $this->query_builder->insert($this->model->table);
        $builder->values($this->property_watcher->getChangedProperties())
            ->execute();

        return $builder->getInsertId();
    }
}