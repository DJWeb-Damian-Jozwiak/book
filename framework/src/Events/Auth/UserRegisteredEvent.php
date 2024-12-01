<?php

declare(strict_types=1);

namespace DJWeb\Framework\Events\Auth;

use DJWeb\Framework\DBAL\Models\Entities\User;
use DJWeb\Framework\Events\BaseEvent;

class UserRegisteredEvent extends BaseEvent
{
    public function __construct(public readonly User $user)
    {
    }
}
