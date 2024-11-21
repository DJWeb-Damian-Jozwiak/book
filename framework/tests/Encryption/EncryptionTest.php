<?php

namespace Encryption;

use DJWeb\Framework\Base\Application;
use DJWeb\Framework\Config\Contracts\ConfigContract;
use DJWeb\Framework\Encryption\EncryptionService;
use DJWeb\Framework\Encryption\KeyGenerator;
use DJWeb\Framework\Storage\EnvFileHandler;
use DJWeb\Framework\Storage\File;
use InvalidArgumentException;
use Tests\BaseTestCase;

class EncryptionTest extends BaseTestCase
{
    public function testEncryptDecryptWithoutKey()
    {
        $app = Application::getInstance();
        $app->bind('base_path', dirname(__DIR__));
        $data = 'test';
        $this->expectException(\RuntimeException::class);
        new EncryptionService()->encrypt($data);
    }

    public function testEncryptDecryptWithWrongKey()
    {
        $app = Application::getInstance();
        $app->bind('base_path', dirname(__DIR__));
        $data = 'test';
        $config = $this->createMock(ConfigContract::class);
        $config->expects($this->once())->method('get')
            ->willReturn('invalid');
        $app->set(ConfigContract::class, $config);
        $this->expectException(\InvalidArgumentException::class);
        new EncryptionService()->encrypt($data);
    }

    public function testEncryptDecrypt()
    {
        $app = Application::getInstance();
        $app->bind('base_path', dirname(__DIR__));
        $data = 'test';
        $config = $this->createMock(ConfigContract::class);
        $config->expects($this->any())->method('get')
            ->willReturn(new KeyGenerator()->generateKey());
        $app->set(ConfigContract::class, $config);
        $encrypted = new EncryptionService()->encrypt($data);
        $encrypted2 = new EncryptionService()->encrypt($data);
        $this->assertNotEquals($data, $encrypted);
        $this->assertNotEquals($encrypted, $encrypted2);
        $decrypted = new EncryptionService()->decrypt($encrypted);
        $this->assertEquals($data, $decrypted);
    }

    public function testThrowsExceptionOnInvalidBase64(): void
    {
        $app = Application::getInstance();
        $app->bind('base_path', dirname(__DIR__));
        $config = $this->createMock(ConfigContract::class);
        $config->expects($this->any())->method('get')
            ->willReturn(new KeyGenerator()->generateKey());
        $app->set(ConfigContract::class, $config);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid base64 encoding');
        new EncryptionService()->decrypt('!@#$%^&*');
    }

    public function testThrowsExceptionOnDataTooShort(): void
    {
        $app = Application::getInstance();
        $app->bind('base_path', dirname(__DIR__));
        $config = $this->createMock(ConfigContract::class);
        $config->expects($this->any())->method('get')
            ->willReturn(new KeyGenerator()->generateKey());
        $app->set(ConfigContract::class, $config);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Data is too short');

        new EncryptionService()->decrypt(base64_encode('short'));
    }

    public function testThrowsExceptionOnDecryptionFailure(): void
    {
        $app = Application::getInstance();
        $app->bind('base_path', dirname(__DIR__));
        $config = $this->createMock(ConfigContract::class);
        $config->expects($this->any())->method('get')
            ->willReturn(new KeyGenerator()->generateKey());
        $app->set(ConfigContract::class, $config);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Decryption failed');

        $fakeNonce = random_bytes(24); // NONCE_LENGTH
        $fakeCiphertext = random_bytes(32);
        $fakeEncrypted = base64_encode($fakeNonce . $fakeCiphertext);

        new EncryptionService()->decrypt($fakeEncrypted);
    }
}