<?php

declare(strict_types=1);

namespace DJWeb\Framework\Encryption;

use DJWeb\Framework\Config\Config;
use InvalidArgumentException;
use RuntimeException;

final class EncryptionService
{
    private const NONCE_LENGTH = SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES;
    private const KEY_LENGTH = SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_KEYBYTES;

    private readonly string $key;

    public function __construct()
    {
        $key = Config::get('app.key');

        if (! $key) {
            throw new RuntimeException(
                'No encryption key set. Run "php console/bin key:generate"'
            );
        }

        $decodedKey = base64_decode($key);

        if ($decodedKey === false || strlen($decodedKey) !== self::KEY_LENGTH) {
            throw new InvalidArgumentException(
                'Invalid encryption key. Key must be exactly ' . self::KEY_LENGTH . ' bytes when decoded.'
            );
        }

        $this->key = $decodedKey;
    }

    public function encrypt(#[\SensitiveParameter] mixed $value): string
    {
        $nonce = random_bytes(self::NONCE_LENGTH);
        $serialized = serialize($value);

        $ciphertext = sodium_crypto_aead_xchacha20poly1305_ietf_encrypt(
            $serialized,
            '',
            $nonce,
            $this->key
        );

        $merged = $nonce . $ciphertext;
        return base64_encode($merged);
    }

    public function decrypt(string $encrypted): mixed
    {
        $decoded = base64_decode($encrypted, true);

        if ($decoded === false) {
            throw new InvalidArgumentException('Invalid base64 encoding');
        }

        if (strlen($decoded) < self::NONCE_LENGTH) {
            throw new InvalidArgumentException('Data is too short');
        }

        $nonce = substr($decoded, 0, self::NONCE_LENGTH);
        $ciphertext = substr($decoded, self::NONCE_LENGTH);

        $decrypted = sodium_crypto_aead_xchacha20poly1305_ietf_decrypt(
            $ciphertext,
            '',
            $nonce,
            $this->key
        );

        if ($decrypted === false) {
            throw new InvalidArgumentException('Decryption failed');
        }

        return unserialize($decrypted);
    }
}
