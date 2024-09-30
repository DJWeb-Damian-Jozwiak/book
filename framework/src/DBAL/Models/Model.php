<?php

namespace DJWeb\Framework\DBAL\Models;

use Carbon\Carbon;
use DJWeb\Framework\DBAL\Models\Contracts\PropertyChangesContract;
use DJWeb\Framework\DBAL\Models\Decorators\EntityInserter;
use DJWeb\Framework\DBAL\Models\Decorators\EntityUpdater;
use DJWeb\Framework\DBAL\Models\QueryBuilders\ModelQueryBuilder;

abstract class Model implements PropertyChangesContract
{
    abstract public string $table { get; }

    public protected(set) string $primary_key_name = 'id';
    public protected(set) ModelQueryBuilder $query_builder;
    public private(set) PropertyWatcher $watcher;
    private readonly EntityUpdater $updater;
    private readonly EntityInserter $inserter;

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

    public function __construct()
    {
        $this->query_builder = new ModelQueryBuilder($this);
        $this->watcher = new PropertyWatcher($this);
        $this->updater = new EntityUpdater($this);
        $this->inserter = new EntityInserter($this);
    }

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
        $this->watcher->markPropertyAsChanged(
            $property_name,
            $this->$property_name
        );
    }

    public function save(): void
    {
        if ($this->watcher->is_new) {
            $this->id = $this->inserter->insert();
        } else {
            $this->updater->update();
        }
    }

    public bool $is_new {
        get => $this->watcher->is_new;
    }

    public static function query(): ModelQueryBuilder
    {
        return new static()->query_builder;
    }

    protected function castAttribute($value, string $type): mixed
    {
        return match(true) {
            $type === 'datetime' => $value instanceof Carbon ? $value : Carbon::parse($value),
        };
    }
}