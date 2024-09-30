<?php

namespace DJWeb\Framework\DBAL\Models\Relations;

use DJWeb\Framework\DBAL\Contracts\Query\QueryBuilderContract;
use DJWeb\Framework\DBAL\Models\Model;
use DJWeb\Framework\DBAL\Models\Relation;

class BelongsTo extends Relation
{
    protected function createQueryBuilder(): QueryBuilderContract
    {
        return $this->parent->query_builder->facade->select(
            $this->related::getTable()
        );
    }

    public function addConstraints(): void
    {
        $this->query->where(
            $this->local_key,
            '=',
            $this->parent->{$this->foreign_key}
        );
    }

    public function getRelated(string $property): array|Model
    {
        $result = $this->getResults();
        return new $this->related()->fill($result);
    }
}