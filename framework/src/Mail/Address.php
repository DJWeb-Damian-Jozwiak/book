<?php

declare(strict_types=1);

namespace DJWeb\Framework\Mail;

use Symfony\Component\Mime\Address as SymfonyAddress;

final readonly class Address
{
    public function __construct(
        public string $email,
        public ?string $name = null
    ) {
    }

    public function toSymfonyAddress(): SymfonyAddress
    {
        return new SymfonyAddress($this->email, $this->name ?? '');
    }
}
