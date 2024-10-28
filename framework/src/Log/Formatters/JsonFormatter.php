<?php

declare(strict_types=1);

namespace DJWeb\Framework\Log\Formatters;

use DJWeb\Framework\Log\Message;

final readonly class JsonFormatter extends Formatter
{
    public function format(Message $message): string
    {
        return json_encode(
            $this->toArray($message),
            JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR
        );
    }

    /**
     * @param Message $message
     *
     * @return array<string, mixed>
     */
    public function toArray(Message $message): array
    {
        return array_filter(
            [
                'level' => $message->level->name,
                'message' => $message->message,
                'context' => $message->context->all(),
                'metadata' => $message->metadata?->toArray(),
            ]
        );
    }
}
