<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Models\Contracts;

interface Castable
{
    public static function from(string $value): static;
}