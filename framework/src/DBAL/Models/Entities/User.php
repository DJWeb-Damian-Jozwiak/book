<?php

namespace DJWeb\Framework\DBAL\Models\Entities;

use Carbon\Carbon;
use DJWeb\Framework\DBAL\Models\Attributes\FakeAs;
use DJWeb\Framework\DBAL\Models\Model;
use DJWeb\Framework\Enums\FakerMethod;

class User extends Model
{
    public protected(set) string $algo = PASSWORD_ARGON2ID;
    protected array $casts = [
        'created_at' => 'datetime',
    ];
    #[FakeAs(FakerMethod::PASSWORD)]
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

    #[FakeAs(FakerMethod::DATE)]
    public Carbon $created_at {
        get => $this->created_at;
        set {
            $this->created_at = $value;
            $this->markPropertyAsChanged('created_at');
        }
    }
    #[FakeAs(FakerMethod::DATE)]
    public Carbon $updated_at {
        get => $this->updated_at;
        set {
            $this->updated_at = $value;
            $this->markPropertyAsChanged('updated_at');
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