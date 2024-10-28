<?php

declare(strict_types=1);

namespace Tests\Log\Formatters;

use Carbon\Carbon;
use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\Enums\Log\LogLevel;
use DJWeb\Framework\Log\Context;
use DJWeb\Framework\Log\Formatters\XmlFormatter;
use DJWeb\Framework\Log\Message;
use PHPUnit\Framework\TestCase;

class XmlFormatterTest extends TestCase
{
    public function testFormat()
    {
        Carbon::setTestNow('2024-10-28 12:00:00');
        $message = new Message(LogLevel::ALERT, 'Test message', new Context(['test' => 'test']));
        $container = $this->createMock(ContainerContract::class);
        $formatter = new XmlFormatter($container);
        $result = $formatter->format($message);
        $expected = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<log>
  <datetime>2024-10-28 12:00:00</datetime>
  <level>ALERT</level>
  <message>Test message</message>
  <context>
    <test>test</test>
  </context>
  <metadata/>
</log>

XML;
        $this->assertEquals($expected, $result);
    }
}