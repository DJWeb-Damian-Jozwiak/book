<?php

declare(strict_types=1);

namespace App\Database\Models;

use Carbon\Carbon;
use DJWeb\Framework\DBAL\Models\Model;

class User extends Model
{
    public string $table {
        get => 'users';
    }

    public string $name {
        get => $this->name;
        set {
            $this->name = $value;
            $this->markPropertyAsChanged('name');
        }
    }
    public string $email {
        get => $this->email;
        set {
            $this->email = $value;
            $this->markPropertyAsChanged('email');
        }
    }
    public string $password {
        get => $this->password;
        set {
            $this->password = $value;
            $this->markPropertyAsChanged('password');
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
        'updated_at' => 'datetime'
    ];
}
