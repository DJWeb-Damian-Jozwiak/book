<?php

declare(strict_types=1);

namespace DJWeb\Framework\Storage\Session;

use DJWeb\Framework\Encryption\EncryptionService;

final class SessionSecurity
{
    public function encrypt(#[\SensitiveParameter] mixed $data): string
    {
        return new EncryptionService()->encrypt($data);
    }

    public function decrypt(string $data): mixed
    {
        return new EncryptionService()->decrypt($data);
    }
}
