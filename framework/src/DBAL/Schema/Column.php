<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Schema;

abstract class Column
{
    public function __construct(
        public readonly string $name,
        public readonly string $type,
        public readonly bool $nullable = true,
        protected mixed $default = null,
    ) {
    }

    abstract public function getSqlDefinition(): string;

    public function getSqlColumn(): string
    {
        return 'mixed';
    }

    public function shouldBeCasted(): bool
    {
        return false;
    }
}
