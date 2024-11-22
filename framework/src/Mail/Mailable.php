<?php

declare(strict_types=1);

namespace DJWeb\Framework\Mail;

use Symfony\Component\Mime\Email;

abstract class Mailable
{
    protected ?Envelope $envelope = null;
    protected ?Content $content = null;

    abstract public function envelope(): Envelope;

    abstract public function content(): Content;

    public function build(): Email
    {
        $this->envelope = $this->envelope();
        $this->content = $this->content();
        $email = new Email();
        $email->from($this->envelope->from->toSymfonyAddress())
            ->subject($this->envelope->subject)
            ->html($this->content->render());
        if ($this->envelope->replyTo !== null) {
            $email->replyTo($this->envelope->replyTo->toSymfonyAddress());

        }

        foreach ($this->envelope->to as $address) {
            $email->addTo($address->toSymfonyAddress());

        }

        foreach ($this->envelope->cc as $address) {
            $email->addCc($address->toSymfonyAddress());

        }

        foreach ($this->envelope->bcc as $address) {
            $email->addBcc($address->toSymfonyAddress());

        }

        return $email;
    }

}
