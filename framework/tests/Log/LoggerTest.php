<?php

declare(strict_types=1);

namespace Tests\Log;

use Carbon\Carbon;
use DJWeb\Framework\Base\Application;
use DJWeb\Framework\Config\Contracts\ConfigContract;
use DJWeb\Framework\DBAL\Contracts\ConnectionContract;
use DJWeb\Framework\Enums\Log\LogLevel;
use DJWeb\Framework\Log\LoggerFactory;
use PHPUnit\Framework\TestCase;

class LoggerTest extends TestCase
{
    public function testCreate()
    {
        Carbon::setTestNow('2024-10-28 12:00:00');
        $app = Application::getInstance();
        $config = $this->createMock(ConfigContract::class);
        $config->expects($this->once())->method('get')
            ->willReturn([
                'default' => 'database',

                'channels' => [
                    'database' => [
                        'handler' => 'database'
                    ],
                ]
            ]);


        $connection = $this->createMock(ConnectionContract::class);
        $connection->expects($this->once())->method('query')
            ->with(
                'INSERT INTO database_logs (level, message, metadata) VALUES (?, ?, ?)',
                [
                    'level' => 'INFO',
                    "message" => "test",
                    'metadata' => [
                        "timestamp" => "2024-10-28 12:00:00",
                    ]
                ]
            )
            ->willReturn(new \PDOStatement());
        $connection->expects($this->once())->method('getLastInsertId')->willReturn('1');
        $app->set(ConnectionContract::class, $connection);
        $app->bind('base_path', dirname(__DIR__));
        $app->set(ConfigContract::class, $config);
        $logger = LoggerFactory::create($app);
        $logger->log(LogLevel::INFO, 'test');
    }
}