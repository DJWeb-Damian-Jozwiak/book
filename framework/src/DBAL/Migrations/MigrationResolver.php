<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Migrations;

use DJWeb\Framework\Base\Application;
use DJWeb\Framework\DBAL\Contracts\Migrations\MigrationResolverContract;
use RuntimeException;

class MigrationResolver implements MigrationResolverContract
{
    public function __construct(private ?string $migrationPath)
    {
    }

    /**
     * @return array<int, string>
     */
    public function getMigrationFiles(): array
    {
        $app = Application::getInstance();
        /** @var string $path */
        $path = $app->getBinding('app.migrations_path');
        $this->migrationPath = $path;
        if (! is_dir($this->migrationPath)) {
            throw new RuntimeException('Migration path does not exist');
        }
        $files = scandir($this->migrationPath);
        $files = array_filter(
            $files,
            static fn (string $file) => $file !== '.' && $file !== '..'
        );
        $files = array_filter($files, $this->isMigrationFile(...));
        /** @var array<int, string> $migrations */
        $migrations = [];
        foreach ($files as $file) {
            $migrations[] = pathinfo($file, PATHINFO_FILENAME);
        }
        \sort($migrations);
        return $migrations;
    }

    public function resolve(string $file): Migration
    {
        $path = $this->migrationPath . DIRECTORY_SEPARATOR . $file . '.php';
        $class = require_once $path;
        $migration = new $class($file);
        if (! $migration instanceof Migration) {
            throw new RuntimeException('Migration class must extend Migration');
        }
        $migration->withName($file);
        return $migration;
    }

    private function isMigrationFile(string $file): bool
    {
        $name = pathinfo($file, PATHINFO_FILENAME);
        return (bool) preg_match('/^\d{4}_\d{2}_\d{2}_\d{6}_\w+$/', $name);
    }
}
