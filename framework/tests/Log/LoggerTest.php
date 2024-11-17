<?php

declare(strict_types=1);

namespace Tests\Log;

use Carbon\Carbon;
use DJWeb\Framework\Base\Application;
use DJWeb\Framework\Config\Contracts\ConfigContract;
use DJWeb\Framework\DBAL\Contracts\ConnectionContract;
use DJWeb\Framework\DBAL\Contracts\Query\InsertQueryBuilderContract;
use DJWeb\Framework\Enums\Log\LogLevel;
use DJWeb\Framework\Log\LoggerFactory;
use PDOStatement;
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


        $builder = $this->createMock(InsertQueryBuilderContract::class);
        $stmt = $this->createMock(PDOStatement::class);
        $builder->expects($this->once())->method('table')->willReturnSelf();
        $builder->expects($this->once())->method('values')->willReturnSelf();
        $builder->expects($this->once())->method('execute')->willReturn($stmt);
        $builder->expects($this->once())->method('getInsertId')->willReturn('1');
        $app->set(InsertQueryBuilderContract::class, $builder);
        $app->bind('base_path', dirname(__DIR__));
        $app->set(ConfigContract::class, $config);
        $logger = LoggerFactory::create($app);
        $logger->log(LogLevel::INFO, 'test');
    }
}