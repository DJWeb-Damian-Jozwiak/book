<?php

namespace Tests\WebSockets;

use DJWeb\Framework\WebSockets\EventDispatcher;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use React\Socket\ConnectionInterface;

class EventDispatcherTest extends TestCase
{
    private EventDispatcher $dispatcher;
    private ConnectionInterface|MockObject $connectionMock;

    protected function setUp(): void
    {
        $this->dispatcher = new EventDispatcher();
        $this->connectionMock = $this->createMock(ConnectionInterface::class);
    }

    public function testAddListener(): void
    {
        $called = false;
        $testData = ['test' => 'data'];

        $this->dispatcher->addListener('test.event', function($data) use (&$called, $testData) {
            $called = true;
            $this->assertEquals($testData, $data);
        });

        $this->dispatcher->dispatch('test.event', $testData, $this->connectionMock);

        $this->assertTrue($called);
    }

    public function testMultipleListeners(): void
    {
        $callCount = 0;
        $testData = ['test' => 'data'];

        for ($i = 0; $i < 3; $i++) {
            $this->dispatcher->addListener('test.event', function($data) use (&$callCount, $testData) {
                $callCount++;
                $this->assertEquals($testData, $data);
            });
        }

        $this->dispatcher->dispatch('test.event', $testData, $this->connectionMock);

        // Sprawdź czy wszystkie callbacki zostały wywołane
        $this->assertEquals(3, $callCount);
    }

    public function testDispatchNonExistentEvent(): void
    {
        $this->dispatcher->dispatch('non.existent.event', [], $this->connectionMock);
        $this->assertTrue(true);
    }

    public function testSend(): void
    {
        $event = 'test.event';
        $testData = ['message' => 'Hello World'];

        $this->connectionMock
            ->expects($this->once())
            ->method('write')
            ->with($this->callback(function($frame) use ($event, $testData) {
                return is_string($frame);
            }));

        $this->dispatcher->send($event, $testData, $this->connectionMock);
    }

    public function testListenerReceivesCorrectArguments(): void
    {
        $testData = ['test' => 'data'];
        $listenerCalled = false;

        $this->dispatcher->addListener('test.event', function($data, $conn, $dispatcher)
        use ($testData, &$listenerCalled) {
            $listenerCalled = true;
            $this->assertEquals($testData, $data);
            $this->assertInstanceOf(ConnectionInterface::class, $conn);
            $this->assertInstanceOf(EventDispatcher::class, $dispatcher);
        });

        $this->dispatcher->dispatch('test.event', $testData, $this->connectionMock);
        $this->assertTrue($listenerCalled);
    }
}