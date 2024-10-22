<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Contracts\Query;

interface InsertQueryBuilderContract extends QueryBuilderContract
{
    /**
     * @param array<int|string, int|string|float|null> $values
     */
    public function values(array $values): self;

    public function execute(): bool|\PDOStatement;

    public function getInsertId(): ?string;
}
