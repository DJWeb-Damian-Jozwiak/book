<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Contracts\Migrations;

use DJWeb\Framework\DBAL\Migrations\Migration;

interface MigrationResolverContract
{
    /**
     * @return array<int, string>
     */
    public function getMigrationFiles(): array;

    public function resolve(string $file): Migration;
}
