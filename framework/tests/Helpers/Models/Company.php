<?php

namespace Tests\Helpers\Models;

use DJWeb\Framework\DBAL\Models\Attributes\HasMany;
use DJWeb\Framework\DBAL\Models\Model;

class Company extends Model
{
    public string $table {
        get => 'companies';
    }

    #[HasMany(Post::class, foreign_key: 'company_id', local_key: 'id')]
    public array $posts {
        get => $this->relations->getRelation('posts');
    }
}