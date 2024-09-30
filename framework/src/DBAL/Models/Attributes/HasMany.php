<?php

namespace DJWeb\Framework\DBAL\Models\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class HasMany
{
    public function __construct(
        public string $related,
        public string $foreign_key,
        public string $local_key
    ) {}
}