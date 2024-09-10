<?php

namespace Tests\Helpers;

readonly class Database
{
    public function __construct(
        public string $dsn,
        public string $username = 'defaultuser',
        public string $password = 'defaultpass'
    ) {}
}