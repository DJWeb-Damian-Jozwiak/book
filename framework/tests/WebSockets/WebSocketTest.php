<?php

namespace Tests\WebSockets;

use DJWeb\Framework\WebSockets\Encoder;
use DJWeb\Framework\WebSockets\EventDispatcher;
use DJWeb\Framework\WebSockets\Frame;
use DJWeb\Framework\WebSockets\Opcode;
use DJWeb\Framework\WebSockets\WebSocket;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use React\Socket\ConnectionInterface;
use SplObjectStorage;

class WebSocketTest extends TestCase
{
    private WebSocket $webSocket;
    private ConnectionInterface|MockObject $connectionMock;
    private SplObjectStorage $connections;
    private EventDispatcher|MockObject $eventDispatcherMock;

    protected function setUp(): void
    {
        $this->connectionMock = $this->createMock(ConnectionInterface::class);
        $this->connections = new SplObjectStorage();
        $this->eventDispatcherMock = $this->createMock(EventDispatcher::class);

        $this->webSocket = new WebSocket(
            $this->connectionMock,
            $this->connections,
            $this->eventDispatcherMock
        );
    }

    public function testInitialize(): void
    {
        $this->connectionMock
            ->expects($this->exactly(2))
            ->method('on')
            ->willReturnCallback(function($event, $callback) {
                static $count = 0;
                $count++;

                match($count) {
                    1 => $this->assertEquals('data', $event),
                    2 => $this->assertEquals('close', $event),
                };

                $this->assertIsCallable($callback);
                return $this->connectionMock;
            });

        $this->webSocket->initialize();
    }

    public function testHandleHandshake(): void
    {
        $key = 'dGhlIHNhbXBsZSBub25jZQ==';
        $handshakeRequest = "GET /chat HTTP/1.1\r\n" .
            "Host: server.example.com\r\n" .
            "Upgrade: websocket\r\n" .
            "Connection: Upgrade\r\n" .
            "Sec-WebSocket-Key: {$key}\r\n" .
            "Sec-WebSocket-Version: 13\r\n\r\n";

        $this->connectionMock
            ->expects($this->once())
            ->method('write')
            ->with($this->callback(function($response) {
                return strpos($response, 'HTTP/1.1 101 Switching Protocols') !== false
                    && strpos($response, 'Upgrade: websocket') !== false
                    && strpos($response, 'Connection: Upgrade') !== false
                    && strpos($response, 'Sec-WebSocket-Accept:') !== false;
            }));

        $this->webSocket->handleData($handshakeRequest);

        $this->assertTrue($this->connections->contains($this->connectionMock));
    }

    public function testHandleInvalidHandshake(): void
    {
        $invalidHandshake = "GET /chat HTTP/1.1\r\n" .
            "Host: server.example.com\r\n\r\n";

        $this->connectionMock
            ->expects($this->once())
            ->method('write')
            ->with("HTTP/1.1 400 Bad Request\r\n\r\n");

        $this->webSocket->handleData($invalidHandshake);
    }

    public function testHandleWebSocketFrame(): void
    {
        // First complete the handshake
        $this->simulateSuccessfulHandshake();

        // Create a mock frame with a JSON message
        $message = json_encode([
            'event' => 'test.event',
            'data' => ['message' => 'Hello World']
        ]);

        $frameData = $this->createMockWebSocketFrame($message);

        $this->eventDispatcherMock
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                'test.event',
                ['message' => 'Hello World'],
                $this->connectionMock
            );

        $this->webSocket->handleData($frameData);
    }

    public function testHandlePingFrame(): void
    {
        $this->simulateSuccessfulHandshake();

        $pingData = "ping payload";
        $pingFrameData = $this->createMockWebSocketFrame($pingData, Opcode::PING);

        $this->connectionMock
            ->expects($this->once())
            ->method('write')
            ->with($this->callback(function($response) {
                return true;
            }));

        $this->webSocket->handleData($pingFrameData);
    }

    public function testHandleClose(): void
    {
        $this->connections->attach($this->connectionMock);
        $this->webSocket->handleClose();
        $this->assertFalse($this->connections->contains($this->connectionMock));
    }

    public function testSendMessage(): void
    {
        $message = "Test message";

        $this->connectionMock
            ->expects($this->once())
            ->method('write')
            ->with($this->callback(function($encodedFrame) {
                return is_string($encodedFrame);
            }));

        $this->webSocket->sendMessage($message);
    }

    private function simulateSuccessfulHandshake(): void
    {
        $key = 'dGhlIHNhbXBsZSBub25jZQ==';
        $handshakeRequest = "GET /chat HTTP/1.1\r\n" .
            "Host: server.example.com\r\n" .
            "Upgrade: websocket\r\n" .
            "Connection: Upgrade\r\n" .
            "Sec-WebSocket-Key: {$key}\r\n" .
            "Sec-WebSocket-Version: 13\r\n\r\n";

        $this->webSocket->handleData($handshakeRequest);
    }

    private function createMockWebSocketFrame(string $payload, Opcode $opcode = Opcode::TEXT): string
    {
        $frame = new Frame(
            true,
            $opcode,
            true,
            strlen($payload),
            str_repeat('*', 4),
            $payload
        );
        return Encoder::encode($frame);
    }
}