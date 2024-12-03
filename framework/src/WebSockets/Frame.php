<?php

declare(strict_types=1);

namespace DJWeb\Framework\WebSockets;

class Frame
{
    public function __construct(
        public bool $fin,
        public Opcode $opcode,
        public bool $mask,
        public int $payloadLength,
        public ?string $maskingKey,
        public string $payload
    ) {
    }
}
