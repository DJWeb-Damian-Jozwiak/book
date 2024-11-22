<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Models\Attributes;

use Attribute;
use DJWeb\Framework\DBAL\Models\Model;

#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class BelongsToMany
{
    /**
     * @param class-string<Model> $related
     * @param string $pivot_table
     * @param string $foreign_pivot_key
     * @param string $related_pivot_key
     */
    public function __construct(
        public string $related,
        public string $pivot_table,
        public string $foreign_pivot_key,
        public string $related_pivot_key
    ) {
    }
}