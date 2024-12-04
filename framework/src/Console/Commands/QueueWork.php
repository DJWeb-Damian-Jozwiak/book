<?php

declare(strict_types=1);

namespace DJWeb\Framework\Console\Commands;

use DJWeb\Framework\Console\Attributes\AsCommand;
use DJWeb\Framework\Console\Command;
use DJWeb\Framework\Scheduler\QueueFactory;
use DJWeb\Framework\Scheduler\Workers\QueueWorker;

#[AsCommand('queue:work')]
class QueueWork extends Command
{
    public function run(): int
    {
        $queue = QueueFactory::make();
        $this->getOutput()->info('Queue worker started.');
        new QueueWorker($queue)->work();
        return 0;
    }
}