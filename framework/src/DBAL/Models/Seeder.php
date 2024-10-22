<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Models;

abstract class Seeder
{
    abstract public function run(): void;

    /**
     * @param class-string<Seeder> $seeder
     */
    protected function call(string $seeder): void
    {
        $instance = new $seeder();
        $instance->run();
    }
}
