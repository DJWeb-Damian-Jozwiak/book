<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Contracts\Query;

interface UpdateQueryBuilderContract extends QueryBuilderContract
{
    /**
     * @param array<string, int|float|string|null> $updates
     */
    public function set(array $updates): self;
    public function execute(): bool;

}
