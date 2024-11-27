<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Models;

use DJWeb\Framework\DBAL\Models\Contracts\NotifyPropertyChangesContract;
use Stringable;

class PropertyObserver implements NotifyPropertyChangesContract
{
    public bool $is_new {
        get => ! isset($this->changedProperties[$this->model->primary_key_name]);
    }
    /**
     * @var array<string, int|string|float|null>
     */
    public private(set) array $changedProperties = [];

    public function __construct(private Model $model)
    {
    }

    public function markPropertyAsChanged(
        string $propertyName,
        mixed $value,
    ): void {
       $this->changedProperties[$propertyName] = $this->toString($value);
    }

    public function toString(mixed $value): mixed
    {
        return match (true) {
            $value instanceof Stringable => $value->toString(),
            is_array($value), is_object($value) => json_encode($value),
            default => $value,
        };
    }

    /**
     * @return array<string, int|string|float|null>
     */
    public function getChangedProperties(): array
    {
        return $this->changedProperties;
    }
}
