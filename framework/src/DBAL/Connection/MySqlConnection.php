<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Connection;

use Carbon\Carbon;
use DJWeb\Framework\Config\Config;
use DJWeb\Framework\DBAL\Contracts\ConnectionContract;
use DJWeb\Framework\Events\Database\QueryExecutedEvent;
use DJWeb\Framework\Events\EventManager;
use PDO;
use SensitiveParameter;

class MySqlConnection implements ConnectionContract
{
    public ?EventManager $eventManager = null;
    private ?PDO $connection = null;

    public function withEventManager(EventManager $eventManager): static
    {
        $this->eventManager = $eventManager;
        return $this;
    }

    public function getConnection(): ?PDO
    {
        return $this->connection;
    }

    public function disconnect(): void
    {
        $this->connection = null;
    }

    /**
     * @param array<string|int, string|float|int|null> $params
     */
    public function query(
        string $sql,
        array $params = []
    ): \PDOStatement|false|null {
        if (! $this->connection) {
            $this->connect();
        }

        $start = microtime(true);

        $statement = $this->connection->prepare($sql);
        /** @phpstan-ignore-next-line */
        $statement->execute(array_values($params));

        $executionTime = microtime(true) - $start;

        $this->eventManager?->dispatch(new QueryExecutedEvent(
            sql: $sql,
            parameters: $params,
            startTime: Carbon::now(),
            executionTime: $executionTime,
            connection: $this->connection->getAttribute(\PDO::ATTR_CONNECTION_STATUS)
        ));

        return $statement;
    }

    public function connect(): void
    {
        if ($this->connection) {
            return;
        }

        $this->connection = $this->connectMysql(
            Config::get('database.mysql.host'),
            (int) Config::get('database.mysql.port'),
            Config::get('database.mysql.database'),
            Config::get('database.mysql.username'),
            Config::get('database.mysql.password'),
            Config::get('database.mysql.charset', 'utf8mb4')
        );
    }

    /**
     * @return array<int, int|false>
     */
    public function getConnectionOptions(): array
    {
        return [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
    }

    public function getLastInsertId(): ?string
    {
        $id = $this->connection?->lastInsertId();
        return $id === false ? null : $id;
    }

    public function connectMysql(
        ?string $host,
        ?int $port,
        ?string $database,
        ?string $username,
        #[SensitiveParameter]
        ?string $password,
        ?string $charset
    ): PDO {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $host,
            $port,
            $database,
            $charset
        );
        return new PDO(
            $dsn,
            $username,
            $password,
            $this->getConnectionOptions()
        );
    }
}
