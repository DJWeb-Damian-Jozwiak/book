<?php

namespace Tests\Helpers\Models;

use DJWeb\Framework\DBAL\Models\Model;

class CompanyUser extends Model
{

    public string $table {
        get => 'company_users';
    }

    public int $company_id {
        get => $this->company_id;
        set {
            $this->company_id = $value;
            $this->markPropertyAsChanged('company_id');
        }
    }

    public int $user_id {
        get => $this->user_id;
        set {
            $this->user_id = $value;
            $this->markPropertyAsChanged('user_id');
        }
    }
}