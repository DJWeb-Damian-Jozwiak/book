<?php

declare(strict_types=1);

namespace DJWeb\Framework\Console\Commands;

use DJWeb\Framework\Console\Attributes\AsCommand;
use DJWeb\Framework\Console\Command;
use DJWeb\Framework\Encryption\KeyGenerator;
use DJWeb\Framework\Storage\EnvFileHandler;

#[AsCommand('key:generate')]
final class KeyGenerateCommand extends Command
{
    private const string KEY_LINE = 'APP_KEY';

    public function run(): int
    {
        $key = new KeyGenerator()->generateKey();
        new EnvFileHandler()->update(self::KEY_LINE, $key);
        return 0;
    }
}
