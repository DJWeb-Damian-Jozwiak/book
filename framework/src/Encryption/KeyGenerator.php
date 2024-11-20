<?php

declare(strict_types=1);

namespace DJWeb\Framework\Encryption;

use Random\Engine\Secure;
use Random\Randomizer;

final class KeyGenerator
{
    private const KEY_LENGTH = 32;

    private readonly Randomizer $randomizer;

    public function __construct()
    {
        $this->randomizer = new Randomizer(new Secure());
    }

    public function generateKey(): string
    {
        $bytes = $this->randomizer->getBytes(self::KEY_LENGTH);
        return base64_encode($bytes);
    }
}