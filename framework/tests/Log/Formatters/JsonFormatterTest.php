<?php

declare(strict_types=1);

namespace Tests\Log\Formatters;

use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\Enums\Log\LogLevel;
use DJWeb\Framework\Log\Context;
use DJWeb\Framework\Log\Formatters\JsonFormatter;
use DJWeb\Framework\Log\Message;
use PHPUnit\Framework\TestCase;

class JsonFormatterTest extends TestCase
{
    public function testFormat()
    {
        $message = new Message(LogLevel::ALERT, 'Test message', new Context(['test' => 'test']));
        $container = $this->createMock(ContainerContract::class);
        $formatter = new JsonFormatter($container);
        $result = $formatter->format($message);
        $this->assertJson('{"level":"ALERT","message":"Test message","context":{"test":"test"}}', $result);
    }
}