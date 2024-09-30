<?php

namespace Tests\Helpers\Models;

use DJWeb\Framework\DBAL\Models\Attributes\HasMany;
use DJWeb\Framework\DBAL\Models\Entities\User;
use DJWeb\Framework\DBAL\Models\Model;

class Company extends Model
{
    public string $table {
        get => 'companies';
    }

    #[HasMany(User::class, 'company_id', 'id')]
    public array $users {
        get => $this->relations->getRelation('users');
    }
}