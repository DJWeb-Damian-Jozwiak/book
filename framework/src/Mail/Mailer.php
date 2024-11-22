<?php

declare(strict_types=1);

namespace DJWeb\Framework\Mail;

use Symfony\Component\Mailer\Mailer as SymfonyMailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;

final readonly class Mailer
{
    private MailerInterface $mailer;
    private MailHistoryLoggerContract $logger;

    public function __construct(
        public string $dsn,
        ?MailerInterface $mailer = null,
        ?MailHistoryLoggerContract $logger = null
    ) {
        $transport = Transport::fromDsn($dsn);
        $this->mailer = $mailer ?? new SymfonyMailer($transport);
        $this->logger = $logger ?? new MailHistoryLogger();
    }

    public function send(Mailable $mail): void
    {
        try {
            $email = $mail->build();
            $this->mailer->send($email);
            $this->logger->logMail($email, 'sent');
        } catch (\Throwable $e) {
            $this->logger->logMail($mail->build(), 'failed', $e->getMessage());
        }
    }
}
