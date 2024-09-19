<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Schema\MySQL;

use DJWeb\Framework\DBAL\Contracts\ConnectionContract;

readonly class Transaction
{
    public function __construct(
        private ConnectionContract $connection,
    ) {
    }

    public function begin(): void
    {
        $this->connection->query('START TRANSACTION');
    }

    public function commit(): void
    {
        $this->connection->query('COMMIT');
    }

    public function rollback(): void
    {
        $this->connection->query('ROLLBACK');
    }
}
