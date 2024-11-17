<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Models;

use Carbon\Carbon;
use DJWeb\Framework\DBAL\Models\Casts\ArrayCaster;
use DJWeb\Framework\DBAL\Models\Contracts\PropertyChangesContract;
use DJWeb\Framework\DBAL\Models\Decorators\EntityManager;
use DJWeb\Framework\DBAL\Models\QueryBuilders\ModelQueryBuilder;
use DJWeb\Framework\DBAL\Models\Relations\RelationDecorator;
use DJWeb\Framework\DBAL\Models\Relations\RelationFactory;

abstract class Model implements PropertyChangesContract
{
    abstract public string $table { get; }

    public protected(set) string $primary_key_name = 'id';
    public protected(set) ModelQueryBuilder $query_builder;
    public private(set) PropertyObserver $observer;
    private RelationFactory $relation_factory;

    private EntityManager $entity_manager;

    protected private(set) RelationDecorator $relations;

    public int|string $id {
        get => $this->id;
        set {
            $this->id = $value;
            $this->markPropertyAsChanged('id');
        }
    }
    /**
     * @var array<string, string>
     */
    protected array $casts = [];

    public final function __construct()
    {
        $this->query_builder = new ModelQueryBuilder($this);
        $this->observer = new PropertyObserver($this);
        $this->entity_manager = new EntityManager($this);
        $this->relation_factory = new RelationFactory();
        $this->relations = new RelationDecorator($this);
    }

    /**
     * @param array<string, mixed> $attributes
     *
     * @return $this
     */
    public function fill(array $attributes): static
    {
        foreach ($attributes as $key => $value) {
            if (! property_exists($this, $key)) {
                continue;
            }
            if (isset($this->casts[$key])) {
                $value = $this->castAttribute($value, $this->casts[$key]);
            }
            $this->$key = $value;
        }
        return $this;
    }

    public function markPropertyAsChanged(string $property_name): void
    {
        $this->observer->markPropertyAsChanged(
            $property_name,
            $this->$property_name
        );
    }

    public function save(): void
    {
        $this->entity_manager->save();
    }

    public bool $is_new {
        get => $this->observer->is_new;
    }

    public static function query(): ModelQueryBuilder
    {
        return new static()->query_builder;
    }

    protected function castAttribute(mixed $value, string $type): mixed
    {
        return match(true) {
            $type === 'datetime' => $value instanceof Carbon ? $value : Carbon::parse($value),
            is_subclass_of($type, \BackedEnum::class) => $type::from($value),
            default => $value,
        };
    }

    public static function getTable(): string
    {
        return new static()->table;
    }
}
