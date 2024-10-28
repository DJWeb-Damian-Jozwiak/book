<?php

declare(strict_types=1);

namespace DJWeb\Framework\Log;

use Carbon\CarbonImmutable;

final readonly class Metadata
{
    /**
     * @param CarbonImmutable $timestamp
     * @param string|null $ip
     * @param string|null $userAgent
     * @param string|null $sessionId
     * @param array<int|string, mixed> $additional
     */
    public function __construct(
        private CarbonImmutable $timestamp,
        private ?string $ip = null,
        private ?string $userAgent = null,
        private ?string $sessionId = null,
        private array $additional = []
    ) {
    }

    public static function create(): self
    {
        return new self(
            timestamp: CarbonImmutable::now(),
            ip: $_SERVER['REMOTE_ADDR'] ?? null,
            userAgent: $_SERVER['HTTP_USER_AGENT'] ?? null
        );
    }

    /**
     * @return array<string|int, mixed>
     */
    public function toArray(): array
    {
        return [
            'timestamp' => $this->timestamp->toDateTimeString(),
            'ip' => $this->ip,
            'userAgent' => $this->userAgent,
            'sessionId' => $this->sessionId,
            ...$this->additional,
        ];
    }
}
