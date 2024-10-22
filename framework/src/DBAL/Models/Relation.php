<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Models;

use DJWeb\Framework\DBAL\Contracts\Query\SelectQueryBuilderContract;
use DJWeb\Framework\DBAL\Models\Contracts\RelationContract;

abstract class Relation implements RelationContract
{
    protected SelectQueryBuilderContract $query;

    /**
     * @param Model $parent
     * @param class-string<Model> $related
     * @param string $foreign_key
     * @param string $local_key
     */
    public function __construct(
        protected Model $parent,
        protected string $related,
        protected string $foreign_key,
        protected string $local_key,
    ) {
        $this->query = $this->createQueryBuilder();
    }
    abstract public function addConstraints(): void;

    /**
     * @return array<int, mixed>
     */
    public function getResults(): array {
        return $this->query->select()->get();
    }
}
