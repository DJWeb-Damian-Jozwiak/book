<?php

declare(strict_types=1);

namespace Tests\Log\Formatters;

use Carbon\Carbon;
use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\Enums\Log\LogLevel;
use DJWeb\Framework\Log\Context;
use DJWeb\Framework\Log\Formatters\TextFormatter;
use DJWeb\Framework\Log\Message;
use PHPUnit\Framework\TestCase;

class TextFormatterTest extends TestCase
{
    public function testFormat()
    {
        Carbon::setTestNow('2024-10-28 12:00:00');
        $message = new Message(LogLevel::ALERT, 'Test message', new Context(['test' => 'test']));
        $container = $this->createMock(ContainerContract::class);
        $formatter = new TextFormatter($container);
        $result = $formatter->format($message);
        $expected = <<<TEXT
[2024-10-28 12:00:00] ALERT: Test message Context: {
    "test": "test"
} Metadata: []

TEXT;

        $this->assertEquals($expected, $result);
    }
}