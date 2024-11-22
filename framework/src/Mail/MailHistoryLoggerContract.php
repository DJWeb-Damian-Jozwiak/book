<?php

declare(strict_types=1);

namespace DJWeb\Framework\Mail;

use Symfony\Component\Mime\Email;

interface MailHistoryLoggerContract
{
    public function logMail(Email $email, string $status, ?string $error = null): void;

}
