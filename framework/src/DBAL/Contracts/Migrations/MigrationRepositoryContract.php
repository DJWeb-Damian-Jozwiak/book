<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Contracts\Migrations;

interface MigrationRepositoryContract
{
    /**
     * @return array<int, string>
     */
    public function getMigrations(): array;

    public function createMigrationsTable(): void;

    public function log(string $migration): void;

    public function delete(string $migration): void;

    /**
     * @return array<int, string>
     */
    public function getRan(): array;
}
