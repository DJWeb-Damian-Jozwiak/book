<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Models\Relations;

use DJWeb\Framework\DBAL\Models\Attributes\BelongsTo as BelongsToAttribute;
use DJWeb\Framework\DBAL\Models\Attributes\BelongsToMany as BelongsToManyAttribute;
use DJWeb\Framework\DBAL\Models\Attributes\HasMany as HasManyAttribute;
use DJWeb\Framework\DBAL\Models\Attributes\HasManyThrough as HasManyThroughAttribute;
use DJWeb\Framework\DBAL\Models\Contracts\RelationContract;
use DJWeb\Framework\DBAL\Models\Model;
use ReflectionAttribute;
use ReflectionProperty;

class RelationDecorator
{
    /**
     * @var array<string, RelationContract|null>
     */
    private array $relations = [];

    /**
     * @var array<string, Model|array<int|string, mixed>>
     */
    private array $relationsCache = [];

    public function __construct(private readonly Model $model)
    {
    }

    /**
     * @param string $name
     *
     * @return Model|array<int|string, Model>|null
     *
     * @throws \ReflectionException
     */
    public function getRelation(string $name): Model|array|null
    {
        if (! isset($this->relations[$name])) {
            $property = new ReflectionProperty($this->model, $name);
            $this->initializeRelation($property);
        }
        $exception = new \RuntimeException("Relation {$name} not found");
        $this->relationsCache[$name]
            ??= $this->relations[$name]?->getRelated() ?? throw $exception;
        return $this->relationsCache[$name];
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
        $this->initializeAllRelations(
            $property,
            BelongsToManyAttribute::class,
            $this->initializeBelongsToMany(...)
        );
        $this->initializeAllRelations(
            $property,
            HasManyThroughAttribute::class,
            $this->initializeHasManyThrough(...)
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
            static fn ($attribute) => $attribute->getName() === $type
        );
        array_walk(
            $attributes,
            static fn (ReflectionAttribute $attribute) => $callback(
                $property,
                $attribute->newInstance()
            )
        );
    }

    private function initializeBelongsToMany(
        ReflectionProperty $property,
        BelongsToManyAttribute $attribute
    ): void {
        $value = RelationFactory::belongsToMany($this->model, $attribute);
        $this->relations[$property->getName()] = $value;
    }

    private function initializeBelongsTo(
        ReflectionProperty $property,
        BelongsToAttribute $attribute
    ): void {
        $value = RelationFactory::belongsTo($this->model, $attribute);
        $this->relations[$property->getName()] = $value;
    }

    private function initializeHasManyThrough(
        ReflectionProperty $property,
        HasManyThroughAttribute $attribute
    ): void {
        $value = RelationFactory::hasManyThrough($this->model, $attribute);
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
