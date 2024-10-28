<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Models;

use DJWeb\Framework\DBAL\Models\Contracts\NotifyPropertyChangesContract;

class PropertyObserver implements NotifyPropertyChangesContract
{
    public bool $is_new {
        get => ! isset($this->changedProperties[$this->model->primary_key_name]);
    }
    /**
     * @var array<string, int|string|float|null>
     */
    private array $changedProperties = [];

    public function markPropertyAsChanged(
        string $propertyName,
        mixed $value,
    ): void {
       $this->changedProperties[$propertyName] = $value;
    }

    /**
     * @return array<string, int|string|float|null>
     */
    public function getChangedProperties(): array
    {
        return $this->changedProperties;
    }
}
