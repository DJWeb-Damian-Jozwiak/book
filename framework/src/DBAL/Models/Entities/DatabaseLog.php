<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Models\Entities;

use DJWeb\Framework\DBAL\Models\Model;
use DJWeb\Framework\Log\Context;
use DJWeb\Framework\Log\Message;
use DJWeb\Framework\Log\Metadata;

class DatabaseLog extends Model
{
    public string $table {
        get => 'database_logs';
    }

    public Message $message {
        get => $this->message;
        set {
            $this->message = $value;
            $this->markPropertyAsChanged('message');
        }
    }

    public Context $context {
        get => $this->context;
        set {
            $this->context = $value;
            $this->markPropertyAsChanged('context');
        }
    }

    public ?Metadata $metadata {
        get => $this->metadata;
        set {
            $this->metadata = $value;
            $this->markPropertyAsChanged('metadata');
        }
    }

    /**
     * @var array<string, string>
     */
    public array $casts = [
        'message' => Message::class,
        'context' => Context::class,
        'metadata' => Metadata::class,
    ];
}
