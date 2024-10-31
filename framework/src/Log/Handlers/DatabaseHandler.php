<?php

declare(strict_types=1);

namespace DJWeb\Framework\Log\Handlers;

use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\DBAL\Models\Entities\DatabaseLog;
use DJWeb\Framework\Log\Contracts\HandlerContract;
use DJWeb\Framework\Log\Formatters\JsonFormatter;
use DJWeb\Framework\Log\Message;

final class DatabaseHandler implements HandlerContract
{
    private readonly JsonFormatter $formatter;

    public function __construct()
    {
        $this->formatter = new JsonFormatter();
    }

    public function handle(Message $message): void
    {
        new DatabaseLog()->fill($this->formatter->toArray($message))->save();
    }

}
