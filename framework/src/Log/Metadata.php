<?php

declare(strict_types=1);

namespace DJWeb\Framework\Log;

use Carbon\CarbonImmutable;

final readonly class Metadata
{
    /**
     * @param CarbonImmutable $timestamp
     */
    public function __construct(
        private CarbonImmutable $timestamp,
    ) {
    }

    public static function create(): self
    {
        return new self(
            timestamp: CarbonImmutable::now(),
        );
    }

    /**
     * @return array<string|int, mixed>
     */
    public function toArray(): array
    {
        return [
            'timestamp' => $this->timestamp->toDateTimeString(),
        ];
    }
}
