<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\Request;

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
