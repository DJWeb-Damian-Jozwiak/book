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

    protected function rootNamespace(): string
    {
        /** @phpstan-ignore-next-line */
        return $this->container->getBinding('app.root_namespace');
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
