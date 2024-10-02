<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Models\Relations;

use DJWeb\Framework\DBAL\Models\Attributes\BelongsTo as BelongsToAttribute;
use DJWeb\Framework\DBAL\Models\Attributes\HasMany as HasManyAttribute;
use DJWeb\Framework\DBAL\Models\Model;
use DJWeb\Framework\DBAL\Models\Relation;
use ReflectionAttribute;
use ReflectionProperty;

class RelationDecorator
{
    /**
     * @var array<string, Relation>
     */
    private array $relations = [];

    private array $relationsCache = [];

    public function getRelation(string $name): Model|array
    {
        if (! isset($this->relations[$name])) {
            $property = new ReflectionProperty($this->model, $name);
            $this->initializeRelation($property);
        }
        $exception =  new \RuntimeException("Relation {$name} not found");
        $this->relationsCache[$name]
            ??= $this->relations[$name]
            ?->getRelated($name) ?? throw $exception;
        return $this->relationsCache[$name];
    }

    public function __construct(private readonly Model $model)
    {
    }

    private function initializeRelation(ReflectionProperty $property): void
    {
        $this->initializeAllRelations(
            $property,
            BelongsToAttribute::class,
            $this->initializeBelongsTo(...)
        );
        $this->initializeAllRelations(
            $property,
            HasManyAttribute::class,
            $this->initializeHasMany(...)
        );
    }

    private function initializeAllRelations(
        ReflectionProperty $property,
        string $type,
        callable $callback
    ): void {
        $attributes = $property->getAttributes();
        $attributes = array_filter(
            $attributes,
            fn($attribute) => $attribute->getName() === $type
        );
        array_walk(
            $attributes,
            fn(ReflectionAttribute $attribute) => $callback(
                $property,
                $attribute->newInstance()
            )
        );
    }

    private function initializeBelongsTo(
        ReflectionProperty $property,
        BelongsToAttribute $attribute
    ): void {
        $value = RelationFactory::belongsTo($this->model, $attribute);
        $this->relations[$property->getName()] = $value;
    }

    private function initializeHasMany(
        ReflectionProperty $property,
        HasManyAttribute $attribute
    ): void {
        $value = RelationFactory::hasMany($this->model, $attribute);
        $this->relations[$property->getName()] = $value;
    }
}
