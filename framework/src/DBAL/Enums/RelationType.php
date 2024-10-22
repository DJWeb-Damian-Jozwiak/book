<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Enums;

enum RelationType: string
{
    case belongsTo = 'belongsTo';
case hasMany = 'hasMany';

}
