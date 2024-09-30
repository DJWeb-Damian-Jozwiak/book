<?php

namespace DJWeb\Framework\DBAL\Models\Decorators;

use DJWeb\Framework\DBAL\Models\Model;

class EntityManager
{
    private readonly EntityUpdater $updater;
    private readonly EntityInserter $inserter;
    public function __construct(private Model $model)
    {
        $this->updater = new EntityUpdater($this->model);
        $this->inserter = new EntityInserter($this->model);
    }

    public function save(): void
    {
        if ($this->model->watcher->is_new) {
            $this->model->id = $this->inserter->insert();
        } else {
            $this->updater->update();
        }
    }
}