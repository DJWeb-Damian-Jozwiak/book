<?php

declare(strict_types=1);

namespace DJWeb\Framework\Console\Commands;

use DJWeb\Framework\Console\Attributes\AsCommand;
use DJWeb\Framework\Console\Command;
use DJWeb\Framework\WebSockets\Listeners\MessageListener;
use DJWeb\Framework\WebSockets\WebSocketServer;

#[AsCommand('ws:start')]
class WsStart extends Command
{
    public function run(): int
    {
        $loop = \React\EventLoop\Loop::get();
        $server = new WebSocketServer($loop, '0.0.0.0', 8080);
        $server->addListener('message', new MessageListener()->listen(...));
        $server->run();
        return 0;
    }
}
