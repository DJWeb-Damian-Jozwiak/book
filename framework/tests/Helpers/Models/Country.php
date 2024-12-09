<?php

declare(strict_types=1);

namespace Tests\Helpers\Models;

use DJWeb\Framework\DBAL\Models\Attributes\HasManyThrough;
use DJWeb\Framework\DBAL\Models\Model;
use Tests\Helpers\Models\Company;
use Tests\Helpers\Models\Post;

class Country extends Model
{
    public string $table {
        get => 'countries';
    }

    public string $name {
        get => $this->name;
        set {
            $this->name = $value;
            $this->markPropertyAsChanged('name');
        }
    }

    // Country has many Posts through Companies
    #[HasManyThrough(
        Post::class,
        Company::class,
        first_key: 'country_id',
        second_key: 'company_id',
        local_key: 'id',
        second_local_key: 'id'
    )]
    public array $posts {
        get {
            /** @var Post[] $models */
            $models = $this->relations->getRelation('posts');
            return $models;
        }
    }
}