<?php

namespace Tests\Helpers\Casts;

use DJWeb\Framework\DBAL\Models\Contracts\Castable;

enum Status : string
{
    case published = 'published';
    case draft = 'draft';
}