<?php

declare(strict_types=1);

namespace DJWeb\Framework\Console\Commands;

use DJWeb\Framework\Console\Attributes\AsCommand;
use DJWeb\Framework\Console\Attributes\CommandArgument;
use DJWeb\Framework\Console\Command;

#[AsCommand(name: 'database:seed')]
class DatabaseSeed extends Command
{
    #[CommandArgument(name: 'seeder', value: 'DatabaseSeeder', description: 'The class name of the root seeder')]
    protected string $seeder = 'DatabaseSeeder';

    public function run(): int
    {
        $seederNamespace = $this->container->getBinding('app.seeder_namespace') ?? 'Database\\Seeders\\';
        $seederClass = $this->rootNamespace() . $seederNamespace . $this->seeder;

        if (! class_exists($seederClass)) {
            $this->getOutput()->error("Seeder class {$seederClass} does not exist.");
            return 1;
        }

        $seeder = new $seederClass();
        $seeder->run();

        $this->getOutput()->info('Database seeding completed successfully.');
        return 0;
    }

    protected function rootNamespace(): string
    {
        return $this->container->getBinding('app.root_namespace');
    }
}
