<?php

namespace App\Mail;

use DJWeb\Framework\Config\Config;
use DJWeb\Framework\DBAL\Models\Entities\User;
use DJWeb\Framework\Mail\Address;
use DJWeb\Framework\Mail\Content;
use DJWeb\Framework\Mail\Envelope;
use DJWeb\Framework\Mail\Mailable;

class PasswordResetMailable extends Mailable
{
    public function __construct(
        private User $user,
        private string $token
    ) {}

    public function content(): Content
    {
        return new Content('mail/password-reset.blade.php', [
            'username' => $this->user->username,
            'resetUrl' => sprintf('%s/reset-password/%s', Config::get('app.url'), $this->token),
            'expiresIn' => '60 minutes'
        ]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(Config::get('mail.from.address'), Config::get('mail.from.name')),
            subject: 'Reset Your Password',
        )->addTo( new Address($this->user->email, $this->user->username));
    }
}