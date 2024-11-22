<?php

declare(strict_types=1);

namespace DJWeb\Framework\Mail;

final class MailerFactory
{
    public static function createSmtpMailer(
        string $host,
        int $port,
        string $username,
        string $password
    ): Mailer {
        $dsn = sprintf(
            'smtp://%s:%s@%s:%d',
            $username,
            $password,
            $host,
            $port
        );
        return new Mailer($dsn);
    }

    public static function createMailHogMailer(
        string $host = 'localhost',
        int $port = 1025
    ): Mailer {
        $dsn = sprintf('smtp://%s:%d', $host, $port);
        return new Mailer($dsn);
    }
}
