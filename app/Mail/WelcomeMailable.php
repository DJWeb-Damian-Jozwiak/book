<?php

declare(strict_types=1);

namespace App\Mail;

use DJWeb\Framework\Config\Config;
use DJWeb\Framework\DBAL\Models\Entities\User;
use DJWeb\Framework\Mail\Address;
use DJWeb\Framework\Mail\Content;
use DJWeb\Framework\Mail\Envelope;
use DJWeb\Framework\Mail\Mailable;

class WelcomeMailable extends Mailable
{
    public function __construct(
        private User $user
    ) {}

    public function content(): Content
    {
        return new Content('mail/welcome.blade.php', [
            'username' => $this->user->username
        ]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(Config::get('mail.from.address'), Config::get('mail.from.name')),
            subject: 'Welcome to ' . Config::get('app.name'),
        )->addTo( new Address($this->user->email, $this->user->username));
    }
}