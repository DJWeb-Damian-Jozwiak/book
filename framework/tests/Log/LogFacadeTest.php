<?php

namespace Tests\Log;

use Carbon\Carbon;
use DJWeb\Framework\Base\Application;
use DJWeb\Framework\Config\Contracts\ConfigContract;
use DJWeb\Framework\DBAL\Contracts\ConnectionContract;
use DJWeb\Framework\DBAL\Contracts\Query\InsertQueryBuilderContract;
use DJWeb\Framework\Enums\Log\LogLevel;
use DJWeb\Framework\Log\Log;
use DJWeb\Framework\Log\LoggerFactory;
use PDOStatement;
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
        $builder = $this->createMock(InsertQueryBuilderContract::class);
        $stmt = $this->createMock(PDOStatement::class);
        $builder->expects($this->once())->method('table')->willReturnSelf();
        $builder->expects($this->once())->method('values')->willReturnSelf();
        $builder->expects($this->once())->method('execute')->willReturn($stmt);
        $builder->expects($this->once())->method('getInsertId')->willReturn('1');
        $app->set(InsertQueryBuilderContract::class, $builder);


        $config = $this->createMock(ConfigContract::class);
        $app->set(ConfigContract::class, $config);
        $config->expects($this->any())->method('get')->willReturn($returnedConfig);

        //
        $app->bind('base_path', dirname(__DIR__));
        $app->set(LoggerInterface::class, LoggerFactory::create());
        $logMethod('{user} {exists} {data} {stringable}, {missing}', ['user' => 'test', 'exists' => true, 'data' => ['test'], 'stringable' => $item]);
    }
}