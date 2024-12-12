<?php

declare(strict_types=1);

namespace DJWeb\Framework\Events\Database;

use DJWeb\Framework\Web\Application;

readonly class QueryLoggerListener
{
    public function __construct(
        private string $logFile = 'sql_queries.log'
    ) {
    }

    public function __invoke(QueryExecutedEvent $event): void
    {
        $app = Application::getInstance();
        $path = $app->base_path . '/storage/logs/' . $this->logFile;
        $logEntry = sprintf(
            "[%s] Query: %s | Params: %s | Time: %.4fms | Connection: %s\n",
            $event->startTime->format('Y-m-d H:i:s.u'),
            $event->sql,
            json_encode($event->parameters),
            $event->executionTime * 1000,
            $event->connection ?? 'unknown'
        );
        $event->stopPropagation();

        file_put_contents($path, $logEntry, FILE_APPEND);
    }
}
