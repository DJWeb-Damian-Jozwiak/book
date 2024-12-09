<?php

declare(strict_types=1);

namespace Tests\Helpers\Models;

use DJWeb\Framework\DBAL\Models\Attributes\BelongsToMany;
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

    #[BelongsToMany(Company::class, 'user_company', 'user_id', 'company_id')]
    public array $companies {
        get => $this->relations->getRelation('companies');
    }

    #[BelongsToMany(
        Role::class,
        pivot_table: 'role_user',
        foreign_pivot_key: 'role_id',
        related_pivot_key: 'user_id',
    )]
    public array $roles {
        get {
            /** @var Role[] $models */
            $models = $this->relations->getRelation('roles');
            return $models;
        }
    }
}