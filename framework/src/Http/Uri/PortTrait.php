<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\Uri;

use InvalidArgumentException;

trait PortTrait
{
    private ?int $port = null;
    public function getPort(): ?int
    {
        return $this->port;
    }
    public function withPort(?int $port): self
    {
        $this->validatePort($port);
        return $this->clone($this, 'port', $port);
    }
    private function validatePort(?int $port): void
    {
        if ($port !== null && ($port < 1 || $port > 65535)) {
            throw new InvalidArgumentException("Invalid port: {$port}");
        }
    }
}
