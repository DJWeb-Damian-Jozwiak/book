<?php

declare(strict_types=1);

namespace DJWeb\Framework\WebSockets;

use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use React\Socket\SocketServer;

class WebSocketServer
{
    private SocketServer $socket;
    private \SplObjectStorage $connections;
    private EventDispatcher $eventDispatcher;

    public function __construct(
        private LoopInterface $loop,
        private string $host,
        private int $port
    ) {
        $this->connections = new \SplObjectStorage();
        $this->eventDispatcher = new EventDispatcher();
    }

    public function run(): void
    {
        $this->socket = new SocketServer("{$this->host}:{$this->port}", [], $this->loop);
        $this->socket->on('connection', [$this, 'handleConnection']);
        $this->loop->run();
    }

    public function addListener(string $event, callable $callback): void
    {
        $this->eventDispatcher->addListener($event, $callback);
    }

    public function handleConnection(ConnectionInterface $connection): void
    {
        $webSocket = new WebSocket($connection, $this->connections, $this->eventDispatcher);
        $webSocket->initialize();
    }
}
