<?php

namespace DJWeb\Framework\Console\Commands;

use DJWeb\Framework\Console\Attributes\AsCommand;
use DJWeb\Framework\Console\Command;

#[AsCommand('list')]
class CommandListCommand extends Command
{

    public function run(): int
    {
        $items = $this->container->get('command');
        return 0;
    }
}