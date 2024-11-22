<?php

declare(strict_types=1);

namespace Tests\Mail;

use DJWeb\Framework\Mail\Mailer;
use DJWeb\Framework\Mail\MailerFactory;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class MailerFactoryTest extends TestCase
{
    public function testCreateSmtpMailer(): void
    {
        $host = 'smtp.example.com';
        $port = 587;
        $username = 'user@example.com';
        $password = 'secret123';

        $mailer = MailerFactory::createSmtpMailer($host, $port, $username, $password);

        $this->assertInstanceOf(Mailer::class, $mailer);

        $expectedDsn = 'smtp://user@example.com:secret123@smtp.example.com:587';
        $this->assertEquals($expectedDsn, $mailer->dsn);
    }

    public function testCreateMailHogMailerWithDefaultValues(): void
    {
        $mailer = MailerFactory::createMailHogMailer();

        $this->assertInstanceOf(Mailer::class, $mailer);

        $expectedDsn = 'smtp://localhost:1025';
        $this->assertEquals($expectedDsn, $mailer->dsn);
    }

}