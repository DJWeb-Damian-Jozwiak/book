<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Contracts\Migrations;

interface MigrationExecutorContract
{
    /**
     * @param array<int, string> $migrations
     *
     * @return array<int, string>
     */
    public function executeMigrations(
        array $migrations,
        string $direction,
        bool $pretend
    ): array;
}
