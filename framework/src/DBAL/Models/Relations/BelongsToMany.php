<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Models\Relations;

use DJWeb\Framework\DBAL\Models\Model;

class BelongsToMany extends HasMany
{
    /**
     * @param Model $parent
     * @param class-string<Model> $related
     * @param string $pivot_table
     * @param string $foreign_pivot_key
     * @param string $related_pivot_key
     */
    public function __construct(
        protected Model $parent,
        protected string $related,
        protected string $pivot_table,
        protected string $foreign_pivot_key,
        protected string $related_pivot_key
    )
    {
        parent::__construct($parent, $related, $foreign_pivot_key, 'id');
    }

    public function addConstraints(): void
    {
        $this->query
            ->innerJoin(
                table: $this->pivot_table,
                first: $this->pivot_table . '.' . $this->related_pivot_key,
                operator: '=',
                second: $this->related::getTable() . '.id'
            )
            ->where(
                $this->pivot_table . '.' . $this->foreign_pivot_key,
                '=',
                $this->parent->id
            );
    }
}
