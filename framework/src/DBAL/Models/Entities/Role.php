<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Models\Entities;

use DJWeb\Framework\DBAL\Models\Attributes\BelongsToMany;
use DJWeb\Framework\DBAL\Models\Model;

class Role extends Model
{
    public string $table {
        get => 'roles';
    }

    public string $name {
        get => $this->name;
        set {
            $this->name = $value;
            $this->markPropertyAsChanged('name');
        }
    }

    public ?string $description {
        get => $this->description;
        set {
            $this->description = $value;
            $this->markPropertyAsChanged('description');
        }
    }

    #[BelongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id')]
    public array $permissions {
        get => $this->relations->getRelation('permissions');
    }

    #[BelongsToMany(User::class, 'user_roles', 'role_id', 'user_id')]
    public array $users {
        get => $this->relations->getRelation('users');
    }

    public function hasPermission(string $permissionName): bool
    {
        return in_array($permissionName, array_column($this->permissions, 'name'));
    }
}
