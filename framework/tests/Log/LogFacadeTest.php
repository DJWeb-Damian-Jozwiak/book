<?php

namespace Tests\Log;

use Carbon\Carbon;
use DJWeb\Framework\Base\Application;
use DJWeb\Framework\Config\Contracts\ConfigContract;
use DJWeb\Framework\DBAL\Contracts\ConnectionContract;
use DJWeb\Framework\Enums\Log\LogLevel;
use DJWeb\Framework\Log\Log;
use DJWeb\Framework\Log\LoggerFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Tests\BaseTestCase;

class LogFacadeTest extends BaseTestCase
{
    public static function logLevelProvider(): array
    {
        return [
            'emergency level' => [LogLevel::EMERGENCY, Log::emergency(...)],
            'alert level' => [LogLevel::ALERT, Log::alert(...)],
            'critical level' => [LogLevel::CRITICAL, Log::critical(...)],
            'error level' => [LogLevel::ERROR, Log::error(...)],
            'warning level' => [LogLevel::WARNING, Log::warning(...)],
            'notice level' => [LogLevel::NOTICE, Log::notice(...)],
            'info level' => [LogLevel::INFO, Log::info(...)],
            'debug level' => [LogLevel::DEBUG, Log::debug(...)],
        ];
    }

    #[DataProvider('logLevelProvider')]
    public function testLog(LogLevel $level, callable $logMethod)
    {
        $returnedConfig = [
            'default' => 'database',

            'channels' => [
                'database' => [
                    'handler' => 'database'
                ],
            ]
        ];
        $stringable = new class implements \Stringable
        {
            public function __toString(): string
            {
                return 'test';
            }
        };
        $item = new $stringable;
        $queryParams = [
            'level' => $level->value,
            "message" => "test true [\"test\"] test, {missing}",
            'metadata' => [
                "timestamp" => "2024-10-28 12:00:00",
            ],
            'context' => [
                'user' => 'test',
                'exists' => true,
                'data' => ['test'],
                'stringable' => $item
            ]
        ];


        Carbon::setTestNow('2024-10-28 12:00:00');
        $app = Application::getInstance();
        $app->bind('base_path', dirname(__DIR__));


        $config = $this->createMock(ConfigContract::class);
        $config->expects($this->any())->method('get')->willReturn($returnedConfig);
        $app->set(ConfigContract::class, $config);
        $connection = $this->createMock(ConnectionContract::class);
        $connection->expects($this->once())->method('query')
            ->with(
                'INSERT INTO database_logs (level, message, context, metadata) VALUES (?, ?, ?, ?)',
                $queryParams
            )
            ->willReturn(new \PDOStatement());
        $connection->expects($this->once())->method('getLastInsertId')->willReturn('1');
        $app->set(ConnectionContract::class, $connection);
        $app->bind('base_path', dirname(__DIR__));
        $app->set(LoggerInterface::class, LoggerFactory::create($app));
        $logMethod('{user} {exists} {data} {stringable}, {missing}', ['user' => 'test', 'exists' => true, 'data' => ['test'], 'stringable' => $item]);
    }
}