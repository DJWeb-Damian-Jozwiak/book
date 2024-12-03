<?php

declare(strict_types=1);

namespace DJWeb\Framework\WebSockets;

use React\Socket\ConnectionInterface;

class EventDispatcher
{
    private array $listeners = [];

    public function addListener(string $event, callable $callback): void
    {
        $this->listeners[$event][] = $callback;
    }

    public function dispatch(string $event, $data, ConnectionInterface $connection): void
    {
        if (isset($this->listeners[$event])) {
            foreach ($this->listeners[$event] as $listener) {
                $listener($data, $connection, $this);
            }
        }
    }

    public function send(string $event, $data, ConnectionInterface $connection): void
    {
        $frame = $this->createFrame($event, $data);
        $connection->write($frame);
    }

    private function createFrame(string $event, $data)
    {
        $payload = json_encode([
            'event' => $event,
            'data' => $data,
        ]);

        $frame = new Frame(
            true,  // fin
            Opcode::TEXT,  // opcode
            false,  // mask (false for server-to-client messages)
            strlen($payload),  // payloadLength
            null,  // maskingKey (null for unmasked messages)
            $payload  // payload
        );

        return Encoder::encode($frame);
    }
}
