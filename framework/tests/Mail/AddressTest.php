<?php

declare(strict_types=1);

namespace Tests\Mail;

use DJWeb\Framework\Mail\Address;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mime\Address as SymfonyAddress;

class AddressTest extends TestCase
{
    public function testConstructWithEmailOnly(): void
    {
        $email = 'test@example.com';
        $address = new Address($email);

        $this->assertSame($email, $address->email);
        $this->assertNull($address->name);
    }

    public function testConstructWithEmailAndName(): void
    {
        $email = 'test@example.com';
        $name = 'Test User';
        $address = new Address($email, $name);

        $this->assertSame($email, $address->email);
        $this->assertSame($name, $address->name);
    }

    public function testToSymfonyAddressWithEmailOnly(): void
    {
        $email = 'test@example.com';
        $address = new Address($email);

        $symfonyAddress = $address->toSymfonyAddress();
        $this->assertInstanceOf(SymfonyAddress::class, $symfonyAddress);
        $this->assertSame($email, $symfonyAddress->getAddress());
        $this->assertSame('', $symfonyAddress->getName());
    }

    public function testToSymfonyAddressWithEmailAndName(): void
    {
        $email = 'test@example.com';
        $name = 'Test User';
        $address = new Address($email, $name);

        $symfonyAddress = $address->toSymfonyAddress();
        $this->assertInstanceOf(SymfonyAddress::class, $symfonyAddress);
        $this->assertSame($email, $symfonyAddress->getAddress());
        $this->assertSame($name, $symfonyAddress->getName());
    }
}