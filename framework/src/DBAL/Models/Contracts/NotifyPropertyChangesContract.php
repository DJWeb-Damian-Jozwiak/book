<?php

namespace DJWeb\Framework\DBAL\Models\Contracts;

interface NotifyPropertyChangesContract
{
    public function markPropertyAsChanged(
        string $propertyName,
        int|string|float|null $value
    ): void;

    /**
     * @return array<int, int|string|float|null>
     */
    public function getChangedProperties(): array;

    public function resetChangedProperties(): void;
}