<?php

declare(strict_types=1);

namespace App\Enums;

enum ButtonType: string
{
    case Primary = 'primary';
    case Secondary = 'secondary';
    case Success = 'success';
    case Danger = 'danger';
    case Warning = 'warning';
    case Info = 'info';
    case Light = 'light';
    case Dark = 'dark';
    case Link = 'link';
}

