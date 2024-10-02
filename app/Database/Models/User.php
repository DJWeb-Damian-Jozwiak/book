<?php

declare(strict_types=1);

namespace App\Database\Models;

use DJWeb\Framework\DBAL\Models\Attributes\FakeAs;
use DJWeb\Framework\DBAL\Models\Entities\User as BaseUser;
use DJWeb\Framework\Enums\FakerMethod;

class User extends BaseUser
{
    public string $table {
        get => 'users';
    }

    #[FakeAs(FakerMethod::NAME)]
    public string $name {
        get => $this->name;
        set {
            $this->name = $value;
            $this->markPropertyAsChanged('name');
        }
    }
    #[FakeAs(FakerMethod::EMAIL)]
    public string $email {
        get => $this->email;
        set {
            $this->email = $value;
            $this->markPropertyAsChanged('email');
        }
    }


    protected array $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
