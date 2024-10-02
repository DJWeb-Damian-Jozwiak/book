<?php

namespace Tests\Helpers\Models;

use DJWeb\Framework\DBAL\Models\Attributes\BelongsTo;
use DJWeb\Framework\DBAL\Models\Model;

class Post extends Model
{
    public string $table {
        get => 'posts';
    }

    public int $company_id {
        get => $this->company_id;
        set {
            $this->company_id = $value;
            $this->markPropertyAsChanged('company_id');
        }
    }

    #[BelongsTo(Company::class, foreign_key: 'company_id', local_key: 'id')]
    public Company $company {
        get {
            /** @var Company $model */
            $model = $this->relations->getRelation('company');
            return $model;
        }
    }
}