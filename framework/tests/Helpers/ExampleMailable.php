<?php

declare(strict_types=1);

namespace Tests\Helpers;

use DJWeb\Framework\Config\Config;
use DJWeb\Framework\Mail\Address;
use DJWeb\Framework\Mail\Content;
use DJWeb\Framework\Mail\Envelope;
use DJWeb\Framework\Mail\Mailable;
use DJWeb\Framework\Mail\MailerFactory;

class ExampleMailable extends Mailable
{
    public function content(): Content
    {
        MailerFactory::createSmtpMailer(...Config::get('mail.default'))->send(new ExampleMailable());
        return new Content('mail/test.blade.php', ['name' => 'test']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            new Address('test@example.com', 'test'),
            'sample email',
            new Address('test2@example.com', 'test')
        )->addTo(new Address('test3@example.com', 'test'))
            ->addBcc(new Address('test4@example.com', 'test'))
            ->addCc(new Address('tes5t@example.com', 'test'));
    }
}