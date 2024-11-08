<?php

declare(strict_types=1);

namespace Tests\Log;

use DJWeb\Framework\Base\Application;
use DJWeb\Framework\Config\Config;
use DJWeb\Framework\Config\Contracts\ConfigContract;
use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\Exceptions\Log\LoggerError;
use DJWeb\Framework\Log\Logger;
use DJWeb\Framework\Log\LoggerFactory;
use PHPUnit\Framework\TestCase;
use Tests\BaseTestCase;

class LoggerFactoryTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->markTestSkipped('Not implemented yet');
    }
    public function testCreate()
    {
        $app = Application::getInstance();
        $config = $this->createMock(ConfigContract::class);
        $config->expects($this->once())->method('get')
            ->willReturn([
                'default' => 'stack',

                'channels' => [
                    'stack' => [
                        'handler' => 'file',
                        'path' => __DIR__.'storage/logs/app.log',
                        'formatter' => 'text',
                        'max_days' => 14
                    ],

                    'database' => [
                        'handler' => 'database'
                    ],

                    'daily' => [
                        'handler' => 'file',
                        'path' =>  __DIR__.'storage/logs/daily.log',
                        'formatter' => 'json',
                        'max_days' => 7
                    ],
                    'xml' => [
                        'handler' => 'file',
                        'path' =>  __DIR__.'storage/logs/daily.log',
                        'formatter' => 'xml',
                        'max_days' => 7
                    ]
                ]
            ]);


        $app->bind('base_path', dirname(__DIR__));
        $app->set(ConfigContract::class, $config);
        $logger = LoggerFactory::create($app);
        $this->assertInstanceOf(Logger::class, $logger);
    }

    public function testInvalidFormatter()
    {
        $app = Application::getInstance();
        $config = $this->createMock(ConfigContract::class);
        $config->expects($this->once())->method('get')
            ->willReturn([
                'default' => 'stack',

                'channels' => [
                    'stack' => [
                        'handler' => 'invalid',
                        'path' => __DIR__.'storage/logs/app.log',
                        'formatter' => 'invalid',
                        'max_days' => 14
                    ],
                ]
            ]);

        $this->expectException(LoggerError::class);
        $app->bind('base_path', dirname(__DIR__));
        $app->set(ConfigContract::class, $config);
        LoggerFactory::create($app);
    }
}