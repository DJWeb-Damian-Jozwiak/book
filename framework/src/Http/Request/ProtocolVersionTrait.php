<?php

namespace DJWeb\Framework\Http\Request;

use Psr\Http\Message\MessageInterface;

trait ProtocolVersionTrait
{
    private string $protocolVersion = '1.1';
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    public function withProtocolVersion(string $version): static
    {
        /** @phpstan-ignore-next-line */
        return $this->clone($this, 'protocolVersion', $version);
    }
}