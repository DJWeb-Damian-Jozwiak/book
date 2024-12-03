<?php

declare(strict_types=1);

namespace DJWeb\Framework\WebSockets;

use React\Socket\Connection;

interface ListenerContract
{
    public function listen(array $data, Connection $connection, EventDispatcher $dispatcher): void;

}
