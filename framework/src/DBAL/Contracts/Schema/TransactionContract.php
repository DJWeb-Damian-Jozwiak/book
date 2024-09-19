<?php

namespace DJWeb\Framework\DBAL\Contracts\Schema;

interface TransactionContract
{
    public function begin(): void;

    public function commit(): void;

    public function rollback(): void;
}