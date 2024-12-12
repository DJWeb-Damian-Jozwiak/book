<?php

declare(strict_types=1);

namespace DJWeb\Framework\Events\Database;

use Carbon\Carbon;
use DJWeb\Framework\Events\BaseEvent;

class QueryExecutedEvent extends BaseEvent
{
    public function __construct(
        public readonly string $sql,
        public readonly array $parameters,
        public readonly Carbon $startTime,
        public readonly ?float $executionTime = null,
        public readonly ?string $connection = null
    ) {
    }
}
