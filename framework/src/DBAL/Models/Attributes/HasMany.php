<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Models\Attributes;

use Attribute;
use DJWeb\Framework\DBAL\Models\Model;

#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class HasMany
{
    /**
     * @param class-string<Model> $related
     * @param string $foreign_key
     * @param string $local_key
     */
    public function __construct(
        public string $related,
        public string $foreign_key,
        public string $local_key
    ) {
    }

}
