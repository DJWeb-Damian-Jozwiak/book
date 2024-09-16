<?php

declare(strict_types=1);

namespace DJWeb\Framework\Console\Output\Contacts;

interface OutputContract
{
    public function write(string $message): void;

    public function writeln(string $message): void;
}
