<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Models\Relations;

use DJWeb\Framework\DBAL\Contracts\Query\SelectQueryBuilderContract;
use DJWeb\Framework\DBAL\Models\Model;
use DJWeb\Framework\DBAL\Models\Relation;

class HasMany extends Relation
{
    public function addConstraints(): void
    {
        $this->query->where(
            $this->foreign_key,
            '=',
            $this->parent->{$this->local_key}
        );
    }

    /**
     * @return array<int, Model>|Model
     */
    public function getRelated(): array|Model
    {
        $results = $this->getResults();
        return array_map(
            fn (array $result) => new $this->related()->fill($result),
            $results
        );
    }
    public function createQueryBuilder(): SelectQueryBuilderContract
    {
        return $this->parent->query_builder->facade
            ->select($this->related::getTable());
    }

}
