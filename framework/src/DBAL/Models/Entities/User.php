<?php

namespace DJWeb\Framework\DBAL\Models\Entities;

use Carbon\Carbon;
use DJWeb\Framework\DBAL\Models\Model;

class User extends Model
{
    public protected(set) string $algo = PASSWORD_ARGON2ID;
    protected array $casts = [
        'created_at' => 'datetime',
    ];
    final public string $password {
        get => $this->password;
        set  {
            $this->password = $this->isHashed($value)
                ? $value : password_hash($value, $this->algo);
            $this->markPropertyAsChanged('password');
        }
    }

    private function isHashed(string $password): bool
    {
        return str_contains($password, '$' . $this->algo);
    }

    public Carbon $created_at {
        get => $this->created_at;
        set {
            $this->created_at = $value;
            $this->markPropertyAsChanged('created_at');
        }
    }

    public string $table {
        get => 'users';
    }
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }


}