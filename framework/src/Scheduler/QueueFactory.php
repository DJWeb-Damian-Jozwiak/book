<?php

declare(strict_types=1);

namespace DJWeb\Framework\Scheduler;

use DJWeb\Framework\Config\Config;
use DJWeb\Framework\Scheduler\Contracts\QueueContract;
use DJWeb\Framework\Scheduler\Queue\DatabaseQueue;
use DJWeb\Framework\Scheduler\Queue\RedisQueue;
use RuntimeException;

class QueueFactory
{
    public static function make(): QueueContract
    {
        $driver = Config::get('queue.default');

        return match ($driver) {
            'database' => new DatabaseQueue(),
            'redis' => new RedisQueue(),
            default => throw new RuntimeException("Unsupported queue driver: {$driver}")
        };
    }
}
