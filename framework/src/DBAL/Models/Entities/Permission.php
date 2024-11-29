<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Models\Entities;

use DJWeb\Framework\DBAL\Models\Attributes\BelongsToMany;
use DJWeb\Framework\DBAL\Models\Model;

class Permission extends Model
{
    public string $table {
        get => 'permissions';
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

    // Relacje
    #[BelongsToMany(Role::class, 'role_permissions', 'permission_id', 'role_id')]
    public array $roles {
        get => $this->relations->getRelation('roles');
    }

}
