<?php

namespace Tests\Helpers\Models;

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

    // Roles can belong to many users (many-to-many)
    #[BelongsToMany(
        User::class,
        pivot_table: 'role_user',
        foreign_pivot_key: 'user_id',
        related_pivot_key: 'role_id',
    )]
    public array $users {
        get {
            /** @var User[] $models */
            $models = $this->relations->getRelation('users');
            return $models;
        }
    }
}