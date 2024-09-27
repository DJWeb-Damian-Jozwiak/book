<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Contracts\Query;

interface QueryBuilderFacadeContract
{
    public function select(string $table): SelectQueryBuilderContract;

    public function insert(string $table): InsertQueryBuilderContract;

    public function update(string $table): UpdateQueryBuilderContract;

    public function delete(string $table): DeleteQueryBuilderContract;
}
