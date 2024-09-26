<?php

declare(strict_types=1);

namespace DJWeb\Framework\Exceptions\DBAL;

use DJWeb\Framework\Exceptions\BaseRuntimeError;

class MigrationsNotFound extends BaseRuntimeError
{
    public function __construct(string $migrations_path)
    {
        parent::__construct("Migrations not found in {$migrations_path}");
    }
}
