<?php

declare(strict_types=1);

namespace Tests\Mail;

use DJWeb\Framework\Mail\Address;
use DJWeb\Framework\Mail\Envelope;
use Error;
use PHPUnit\Framework\TestCase;

class EnvelopeTest extends TestCase
{
    private Address $from;
    private Address $replyTo;
    private string $subject;
    private Envelope $envelope;

    protected function setUp(): void
    {
        $this->from = new Address('sender@example.com', 'Sender Name');
        $this->replyTo = new Address('reply@example.com', 'Reply Name');
        $this->subject = 'Test Subject';
        $this->envelope = new Envelope($this->from, $this->subject, $this->replyTo);
    }

    public function testConstructor(): void
    {
        $envelope = new Envelope($this->from, $this->subject);

        $this->assertSame($this->from, $envelope->from);
        $this->assertSame($this->subject, $envelope->subject);
        $this->assertNull($envelope->replyTo);
        $this->assertEmpty($envelope->to);
        $this->assertEmpty($envelope->cc);
        $this->assertEmpty($envelope->bcc);
    }

    public function testConstructorWithReplyTo(): void
    {
        $envelope = new Envelope($this->from, $this->subject, $this->replyTo);

        $this->assertSame($this->from, $envelope->from);
        $this->assertSame($this->subject, $envelope->subject);
        $this->assertSame($this->replyTo, $envelope->replyTo);
    }

    public function testAddTo(): void
    {
        $recipient1 = new Address('recipient1@example.com');
        $recipient2 = new Address('recipient2@example.com');

        $result = $this->envelope->addTo($recipient1)->addTo($recipient2);

        $this->assertSame($this->envelope, $result);
        $this->assertCount(2, $this->envelope->to);
        $this->assertSame($recipient1, $this->envelope->to[0]);
        $this->assertSame($recipient2, $this->envelope->to[1]);
    }

    public function testAddCc(): void
    {
        $cc1 = new Address('cc1@example.com');
        $cc2 = new Address('cc2@example.com');

        $result = $this->envelope->addCc($cc1)->addCc($cc2);

        $this->assertSame($this->envelope, $result);
        $this->assertCount(2, $this->envelope->cc);
        $this->assertSame($cc1, $this->envelope->cc[0]);
        $this->assertSame($cc2, $this->envelope->cc[1]);
    }

    public function testAddBcc(): void
    {
        $bcc1 = new Address('bcc1@example.com');
        $bcc2 = new Address('bcc2@example.com');

        $result = $this->envelope->addBcc($bcc1)->addBcc($bcc2);

        $this->assertSame($this->envelope, $result);
        $this->assertCount(2, $this->envelope->bcc);
        $this->assertSame($bcc1, $this->envelope->bcc[0]);
        $this->assertSame($bcc2, $this->envelope->bcc[1]);
    }

    public function testArraysAreReadOnly(): void
    {
        $this->expectException(Error::class);
        $this->envelope->to = [];
    }
}