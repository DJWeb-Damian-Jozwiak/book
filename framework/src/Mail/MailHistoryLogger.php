<?php

declare(strict_types=1);

namespace DJWeb\Framework\Mail;

use DJWeb\Framework\DBAL\Models\Entities\MailHistory;
use Symfony\Component\Mime\Email;

readonly class MailHistoryLogger implements MailHistoryLoggerContract
{
    public function logMail(Email $email, string $status, ?string $error = null): void
    {
        $history = new MailHistory();
        $history->from_email = $email->getFrom()[0]->getAddress();
        $history->from_name = $email->getFrom()[0]->getName();
        $history->subject = $email->getSubject();

        if ($email->getTo()) {
            $history->to_email = $email->getTo()[0]->getAddress();
            $history->to_name = $email->getTo()[0]->getName();
        }

        $history->cc_email = $this->cc($email);
        $history->bcc_email = $this->bcc($email);

        if ($email->getReplyTo()) {
            $history->reply_to_email = $email->getReplyTo()[0]->getAddress();
        }

        $history->status = $status;
        $history->error = $error;

        $history->save();
    }

    public function cc(Email $email): string
    {
        return implode(',', array_map(
            static fn ($address) => $address->getAddress(),
            $email->getCc()
        ));
    }

    public function bcc(Email $email): string
    {
        return implode(',', array_map(
            static fn ($address) => $address->getAddress(),
            $email->getBcc()
        ));
    }
}
