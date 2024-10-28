<?php

declare(strict_types=1);

namespace DJWeb\Framework\Log;

use DJWeb\Framework\Enums\Log\LogLevel;

final readonly class Message
{
    public function __construct(
        public LogLevel $level,
        public string $message,
        public Context $context,
        public ?Metadata $metadata = null
    )
    {
    }
}
