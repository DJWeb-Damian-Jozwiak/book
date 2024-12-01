<?php

declare(strict_types=1);

namespace DJWeb\Framework\Events\Auth;

use App\Mail\WelcomeMailable;
use DJWeb\Framework\Config\Config;
use DJWeb\Framework\Mail\MailerFactory;

class EmailNotificationListener
{
    public function __invoke(UserRegisteredEvent $event): void
    {
        MailerFactory::createSmtpMailer(...Config::get('mail.default'))
            ->send(new WelcomeMailable($event->user));
    }
}
