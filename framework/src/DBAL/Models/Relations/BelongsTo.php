<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Models\Relations;

use DJWeb\Framework\DBAL\Contracts\Query\SelectQueryBuilderContract;
use DJWeb\Framework\DBAL\Models\Model;
use DJWeb\Framework\DBAL\Models\Relation;

class BelongsTo extends Relation
{
    public function addConstraints(): void
    {
        $this->query->where(
            $this->local_key,
            '=',
            $this->parent->{$this->foreign_key}
        );
    }

    /**
     * @return array<int, Model>|Model
     */
    public function getRelated(): array|Model
    {
        $result = $this->getResults();
        return new $this->related()->fill($result[0]);
    }
    public function createQueryBuilder(): SelectQueryBuilderContract
    {
        return $this->parent->query_builder->facade->select(
            $this->related::getTable()
        );
    }
}
