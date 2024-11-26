<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Models\Entities;

use Carbon\Carbon;
use DJWeb\Framework\DBAL\Models\Attributes\BelongsToMany;
use DJWeb\Framework\DBAL\Models\Model;

class User extends Model
{
    public string $table {
        get => 'users';
    }

    public ?string $email {
        get => $this->email;
        set {
            $this->email = $value;
            $this->markPropertyAsChanged('email');
        }
    }
    public ?string $username {
        get => $this->username;
        set {
            $this->username = $value;
            $this->markPropertyAsChanged('username');
        }
    }
    public string $password {
        get => $this->password;
        set {
            $this->password = $value;
            $this->markPropertyAsChanged('password');
        }
    }
    public ?string $remember_token {
        get => $this->remember_token;
        set {
            $this->remember_token = $value;
            $this->markPropertyAsChanged('remember_token');
        }
    }
    public ?string $password_reset_token {
        get => $this->password_reset_token;
        set {
            $this->password_reset_token = $value;
            $this->markPropertyAsChanged('password_reset_token');
        }
    }
    public ?Carbon $password_reset_expires {
        get => $this->password_reset_expires;
        set {
            $this->password_reset_expires = $value;
            $this->markPropertyAsChanged('password_reset_expires');
        }
    }
    public ?Carbon $email_verified_at {
        get => $this->email_verified_at;
        set {
            $this->email_verified_at = $value;
            $this->markPropertyAsChanged('email_verified_at');
        }
    }
    public int $is_active {
        get => $this->is_active;
        set {
            $this->is_active = $value;
            $this->markPropertyAsChanged('is_active');
        }
    }
    public ?Carbon $last_login_at {
        get => $this->last_login_at;
        set {
            $this->last_login_at = $value;
            $this->markPropertyAsChanged('last_login_at');
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

    #[BelongsToMany(Role::class, 'user_roles', 'user_id', 'role_id')]
    public array $roles {
        get => $this->relations->getRelation('roles');
    }

    public function hasRole(string $roleName): bool
    {
        return array_any($this->roles, fn(Role $role) => $role->name === $roleName);
    }

    public function hasPermission(string $permissionName): bool
    {
        return array_any($this->roles, fn(Role $role) => $role->hasPermission($permissionName));
    }

    protected array $casts = [
        'password_reset_expires' => 'datetime',
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
