<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Models\Entities;

use Carbon\Carbon;
use DJWeb\Framework\DBAL\Models\Model;

class Session extends Model
{
    public string $table {
        get => 'sessions';
    }

    public ?string $payload {
        get => $this->payload;
        set {
            $this->payload = $value;
            $this->markPropertyAsChanged('payload');
        }
    }
    public ?int $last_activity {
        get => $this->last_activity;
        set {
            $this->last_activity = $value;
            $this->markPropertyAsChanged('last_activity');
        }
    }
    public ?string $user_ip {
        get => $this->user_ip;
        set {
            $this->user_ip = $value;
            $this->markPropertyAsChanged('user_ip');
        }
    }
    public ?string $user_agent {
        get => $this->user_agent;
        set {
            $this->user_agent = $value;
            $this->markPropertyAsChanged('user_agent');
        }
    }
    public ?int $user_id {
        get => $this->user_id;
        set {
            $this->user_id = $value;
            $this->markPropertyAsChanged('user_id');
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

    /**
     * @var array<string, string>
     */
    protected array $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
