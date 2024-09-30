<?php

namespace DJWeb\Framework\DBAL\Models\Decorators;

use DJWeb\Framework\DBAL\Contracts\Query\QueryBuilderFacadeContract;
use DJWeb\Framework\DBAL\Models\Model;
use DJWeb\Framework\DBAL\Models\PropertyWatcher;

class EntityUpdater
{
    private QueryBuilderFacadeContract $query_builder;
    private PropertyWatcher $property_watcher;
    public function __construct(

        private Model $model
    ) {
        $this->query_builder = $this->model->query_builder->builder;
        $this->property_watcher = $this->model->watcher;
    }

    public function update(): void
    {
        $this->query_builder->update($this->model->table)
            ->set($this->property_watcher->getChangedProperties());
    }
}