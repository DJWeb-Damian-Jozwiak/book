<?php

namespace App\Console\Commands;

use DJWeb\Framework\Console\Attributes\AsCommand;
use DJWeb\Framework\Console\Command;

#[AsCommand('hello_world')]
class HelloWorldCommand extends Command
{
    public function run(): int
    {
        $this->getOutput()->info('Hello World from console!');
        return 0;
    }
}