<?php

declare(strict_types=1);

namespace DJWeb\Framework\Console\Commands;

use Carbon\Carbon;
use DJWeb\Framework\Console\Attributes\AsCommand;

#[AsCommand(name: 'make:migration')]
class MakeMigration extends MakeCommand
{
    protected function getStub(): string
    {
        $dir = dirname(__DIR__, 3);

        return $dir . '/stubs/migration.stub';
    }

    protected function getDefaultNamespace(): string
    {
        return $this->rootNamespace() . 'Database\\Migrations';
    }

    protected function getPath(string $name): string
    {
        $name = str_replace('\\', '/', $name);

        return $this->container->getBinding(
                'app.migrations_path'
            ) . '/' . $name;
    }

    protected function qualifyName(string $name): string
    {
        if (! str_contains($name, '_table')) {
            $name .= '_table';
        }

        $name = parent::qualifyName($name);

        return Carbon::now()->format('Y_m_d_His_') . $name;
    }
}
