<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\Uri;

class Authority
{
    private const DEFAULT_PORTS = [
        'http' => 80,
        'https' => 443,
    ];

    private int $defaultPort;

    public function __construct()
    {
        $scheme = Scheme::get();
        $this->defaultPort = self::DEFAULT_PORTS[$scheme] ?? 0;
    }
    public function get(): string
    {
        $authority = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '';
        $port = $_SERVER['HTTP_X_FORWARDED_PORT'] ?? $_SERVER['SERVER_PORT'] ?? $this->defaultPort;

        return (int) $port === $this->defaultPort ? $authority : $authority . ':' . $port;
    }
}
