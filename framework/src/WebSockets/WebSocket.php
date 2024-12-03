<?php

declare(strict_types=1);

namespace DJWeb\Framework\WebSockets;

use DJWeb\Framework\Log\Log;
use React\Socket\ConnectionInterface;
use SplObjectStorage;

class WebSocket
{
    private string $buffer = '';
    private bool $handshakeCompleted = false;

    public function __construct(
        private ConnectionInterface $connection,
        private SplObjectStorage $connections,
        private EventDispatcher $eventDispatcher
    ) {
    }

    public function initialize(): void
    {
        $this->connection->on('data', function ($data): void {
            $this->handleData($data);
        });
        $this->connection->on('close', function (): void {
            $this->handleClose();
        });
    }

    public function handleData(string $data): void
    {
        try {
            if (! $this->handshakeCompleted) {
                $this->handleHandshake($data);
            } else {
                $this->handleWebSocketFrame($data);
            }
        } catch (\Exception $e) {
            dump($e);
            Log::error($e->getMessage());
        }
    }

    public function handleClose(): void
    {
        $this->connections->detach($this->connection);
    }

    public function sendMessage(string $message): void
    {
        $frame = new Frame(
            true,
            Opcode::TEXT,
            false,
            strlen($message),
            null,
            $message
        );
        $encodedFrame = Encoder::encode($frame);
        $this->connection->write($encodedFrame);
    }

    private function handleHandshake(string $data): void
    {
        $this->buffer .= $data;
        if (str_contains($this->buffer, "\r\n\r\n")) {
             $response = $this->performHandshake($this->buffer);
            $this->connection->write($response);
            $this->handshakeCompleted = true;
            $this->connections->attach($this->connection);
            $this->buffer = '';
        }
    }

    private function performHandshake(string $request): string
    {
        $lines = preg_split("/\r\n/", $request);
        $headers = [];
        foreach ($lines as $line) {
            $parts = explode(': ', $line);
            if (count($parts) === 2) {
                $headers[$parts[0]] = $parts[1];
            }
        }

        if (isset($headers['Sec-WebSocket-Key'])) {
            $secWebSocketKey = $headers['Sec-WebSocket-Key'];

            $secWebSocketAccept = base64_encode(pack('H*', sha1($secWebSocketKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
            return "HTTP/1.1 101 Switching Protocols\r\n" .
                "Upgrade: websocket\r\n" .
                "Connection: Upgrade\r\n" .
                "Sec-WebSocket-Accept: {$secWebSocketAccept}\r\n\r\n";
        }

        return "HTTP/1.1 400 Bad Request\r\n\r\n";
    }
    private function handleWebSocketFrame(string $data): void
    {
        $frame = Decoder::decode($data);
        if ($frame->opcode === Opcode::TEXT) {
            $payload = $frame->payload;
            $decodedMessage = json_decode($payload, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($decodedMessage['event'])) {
                $this->eventDispatcher->dispatch($decodedMessage['event'], $decodedMessage['data'] ?? null, $this->connection);
            }
        }
        if ($frame->opcode === Opcode::PING)
        {
           $this->handlePingFrame($frame);
        }
    }

    private function handlePingFrame(Frame $frame): void
    {
        // Respond with a PONG frame
        $pongFrame = new Frame(
            true,
            Opcode::PONG,
            false,
            strlen($frame->payload),
            null,
            $frame->payload
        );
        $encodedPongFrame = Encoder::encode($pongFrame);
        $this->connection->write($encodedPongFrame);
    }
}
