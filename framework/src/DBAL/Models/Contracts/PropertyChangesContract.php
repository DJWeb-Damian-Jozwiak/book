<?php

namespace DJWeb\Framework\DBAL\Models\Contracts;

interface PropertyChangesContract
{
    public function markPropertyAsChanged(string $property_name): void;
}