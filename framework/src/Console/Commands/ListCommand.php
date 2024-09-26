<?php

namespace DJWeb\Framework\Console\Commands;

use DJWeb\Framework\Console\Attributes\AsCommand;
use DJWeb\Framework\Console\Command;

#[AsCommand('list')]
class ListCommand extends Command
{
    public function run(): int
    {
        $items = $this->container->get('command');
        $this->getOutput()->info('Available commands:');
        foreach ($items as $name => $item) {
            $this->getOutput()->write('php console/bin ');
            $this->getOutput()->info($name);
        }
        return 0;
    }
}