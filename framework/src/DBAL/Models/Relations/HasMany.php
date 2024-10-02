<?php

namespace DJWeb\Framework\DBAL\Models\Relations;

use DJWeb\Framework\DBAL\Contracts\Query\QueryBuilderContract;
use DJWeb\Framework\DBAL\Models\Model;
use DJWeb\Framework\DBAL\Models\Relation;

class HasMany extends Relation
{

    protected function createQueryBuilder(): QueryBuilderContract
    {
        return $this->parent->query_builder->facade
            ->select($this->related::getTable());
    }

    public function addConstraints(): void
    {
        $this->query->where(
            $this->foreign_key,
            '=',
            $this->parent->{$this->local_key}
        );
    }

    /**
     * @param string $property
     * @return array<int, Model>|Model
     */
    public function getRelated(string $property): array|Model
    {
        $results = $this->getResults();
        return array_map(
            fn (array $result) => new $this->related()->fill($result),
            $results
        );
    }
}