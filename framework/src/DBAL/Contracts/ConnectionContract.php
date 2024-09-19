<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Contracts;

interface ConnectionContract
{
    public function connect(): void;
    public function disconnect(): void;

    /**
     * @param array<string|int, string|float|int|null> $params
     */
    public function query(string $sql, array $params = []): \PDOStatement|false;

    /**
     * @return array<int, int>
     */
    public function getConnectionOptions(): array;
}
