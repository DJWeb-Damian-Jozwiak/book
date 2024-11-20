<?php

declare(strict_types=1);

namespace App\Database\Models;

use Carbon\Carbon;
use DJWeb\Framework\DBAL\Models\Model;

class DatabaseLog extends Model
{
    public string $table {
        get => 'database_logs';
    }

    public string $level {
        get => $this->level;
        set {
            $this->level = $value;
            $this->markPropertyAsChanged('level');
        }
    }
    public string $message {
        get => $this->message;
        set {
            $this->message = $value;
            $this->markPropertyAsChanged('message');
        }
    }
    public string $metadata {
        get => $this->metadata;
        set {
            $this->metadata = $value;
            $this->markPropertyAsChanged('metadata');
        }
    }
    public string $context {
        get => $this->context;
        set {
            $this->context = $value;
            $this->markPropertyAsChanged('context');
        }
    }
    public Carbon $created_at {
        get => $this->created_at;
        set {
            $this->created_at = $value;
            $this->markPropertyAsChanged('created_at');
        }
    }
    public Carbon $updated_at {
        get => $this->updated_at;
        set {
            $this->updated_at = $value;
            $this->markPropertyAsChanged('updated_at');
        }
    }

    protected array $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'metadata' => 'array',
        'context' => 'array'
    ];
}


