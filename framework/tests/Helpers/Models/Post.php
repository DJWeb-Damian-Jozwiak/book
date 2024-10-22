<?php

namespace Tests\Helpers\Models;

use DJWeb\Framework\DBAL\Models\Attributes\BelongsTo;
use DJWeb\Framework\DBAL\Models\Attributes\FakeAs;
use DJWeb\Framework\DBAL\Models\Model;
use DJWeb\Framework\Enums\FakerMethod;
use Tests\Helpers\Casts\Status;

class Post extends Model
{
    public string $table {
        get => 'posts';
    }

    public Status $status {
        get => $this->status;
        set {
            $this->status = $value;
            $this->markPropertyAsChanged('status');
        }
    }
    #[FakeAs(FakerMethod::NAME)]
    public string $name {
        get => $this->name;
        set {
            $this->name = $value;
            $this->markPropertyAsChanged('name');
        }
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

    protected array $casts = [
        'published_at' => 'datetime',
        'status' => Status::class,
    ];
}