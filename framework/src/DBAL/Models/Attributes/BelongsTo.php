<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Models\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class BelongsTo
{
    public function __construct(
        public string $related,
        public string $foreign_key,
        public string $local_key
    ) {
    }

}
