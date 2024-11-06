<?php

declare(strict_types=1);

namespace DJWeb\Framework\Enums;

enum MiddlewarePosition: string
{
    case before_global = 'before_global';
case after_global = 'after_global';

}
