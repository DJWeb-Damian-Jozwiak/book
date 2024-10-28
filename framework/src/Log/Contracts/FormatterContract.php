<?php

declare(strict_types=1);

namespace DJWeb\Framework\Log\Contracts;

use DJWeb\Framework\Log\Message;

interface FormatterContract
{
    public function format(Message $message): string;
}
