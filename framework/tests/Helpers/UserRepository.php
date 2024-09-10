<?php

namespace Tests\Helpers;

use Tests\Helpers\Database;

readonly class UserRepository
{
    public function __construct(public Database $database) {}
}