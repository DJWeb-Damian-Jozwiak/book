<?php

namespace DJWeb\Framework\DBAL\Models\Entities;

use DJWeb\Framework\DBAL\Models\Model;

class Test extends Model
{
    public string $table {
        get => 'tests';
    }
}