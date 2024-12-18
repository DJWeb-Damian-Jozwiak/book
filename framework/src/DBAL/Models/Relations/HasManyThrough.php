<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Models\Relations;

use DJWeb\Framework\DBAL\Models\Model;

class HasManyThrough extends HasMany
{
    /**
     * @param Model $parent
     * @param class-string<Model> $related
     * @param class-string<Model> $through
     * @param string $first_key
     * @param string $second_key
     * @param string $local_key
     * @param string $second_local_key
     */
    public function __construct(
        protected Model $parent,
        protected string $related,
        protected string $through,
        protected string $first_key,
        protected string $second_key,
        protected string $local_key,
        protected string $second_local_key
    )
    {
        parent::__construct($parent, $related, $first_key, $local_key);
    }

    public function addConstraints(): void
    {
        $this->query
            ->innerJoin(
                table: $this->through::getTable(),
                first: $this->through::getTable() . '.' . $this->second_local_key,
                operator: '=',
                second: $this->related::getTable() . '.' . $this->second_key
            )
            ->where(
                $this->through::getTable() . '.' . $this->first_key,
                '=',
                $this->parent->id
            );
    }
}
