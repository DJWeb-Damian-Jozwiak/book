<?php

namespace DJWeb\Framework\Http\Uri;

trait HostTrait
{
    private string $host = '';
    public function getHost(): string
    {
        return $this->host;
    }
    public function withHost(string $host): self
    {
        return $this->clone($this, 'host', $host);
    }
}