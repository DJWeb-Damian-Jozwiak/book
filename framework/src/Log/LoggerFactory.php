<?php

declare(strict_types=1);

namespace DJWeb\Framework\Log;

use DJWeb\Framework\Config\Config;
use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\Exceptions\Log\LoggerError;
use DJWeb\Framework\Log\Formatters\JsonFormatter;
use DJWeb\Framework\Log\Formatters\TextFormatter;
use DJWeb\Framework\Log\Formatters\XmlFormatter;
use DJWeb\Framework\Log\Handlers\DatabaseHandler;
use DJWeb\Framework\Log\Handlers\FileHandler;
use DJWeb\Framework\Log\Rotators\DailyRotator;
use Psr\Log\LoggerInterface;

class LoggerFactory
{
    public static function create(ContainerContract $container): LoggerInterface
    {
        $config = Config::get('logging');
$handlers = [];
foreach ($config['channels'] as $channel => $settings) {
            $handlers[] = match($settings['handler']) {
                'database' => new DatabaseHandler($container),
                'file' => new FileHandler(
                    logPath: $settings['path'],
                    formatter: match($settings['formatter']) {
                        'json' => new JsonFormatter($container),
                        'xml' => new XmlFormatter($container),
                        default => new TextFormatter($container)

                    },
                    rotator: new DailyRotator(
                        maxDays: $settings['max_days'] ?? 7
                    )
                ),
                default => throw new LoggerError("Unknown handler type: {$settings['handler']}")

            };

}

        return new Logger($handlers);
    }

}
