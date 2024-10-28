<?php

declare(strict_types=1);

namespace DJWeb\Framework\Log\Contracts;

use DJWeb\Framework\Log\Message;

interface HandlerContract
{
    public function handle(Message $message): void;
}
