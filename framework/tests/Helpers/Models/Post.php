<?php

namespace Tests\Helpers\Models;

use DJWeb\Framework\DBAL\Models\Attributes\BelongsTo;
use DJWeb\Framework\DBAL\Models\Entities\User;
use DJWeb\Framework\DBAL\Models\Model;

class Post extends Model
{
    public string $table {
        get => 'posts';
    }

    public int $user_id {
        get => $this->user_id;
        set {
            $this->user_id = $value;
            $this->markPropertyAsChanged('user_id');
        }
    }

    #[BelongsTo(User::class, 'user_id', 'id')]
    public User $user {
        get {
            /** @var User $model */
            $model = $this->relations->getRelation('user');
            return $model;
        }
    }
}