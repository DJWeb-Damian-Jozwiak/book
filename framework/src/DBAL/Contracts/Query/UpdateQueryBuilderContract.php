<?php

namespace DJWeb\Framework\DBAL\Contracts\Query;

interface UpdateQueryBuilderContract extends QueryBuilderContract
{
    /**
     * @param array<string, int|float|string> $updates
     *
     * @return $this
     */
    public function set(array $updates): self;
}