<?php

declare(strict_types=1);

namespace DJWeb\Framework\Events\Database;

class QueryExecutedEvent
{
    public function __construct(
        public readonly string $sql,
        public readonly array $parameters,
        public readonly \DateTimeImmutable $startTime,
        public readonly ?float $executionTime = null,
        public readonly ?string $connection = null
    ) {
    }
}
