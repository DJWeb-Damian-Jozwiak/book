<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Contracts\Query;

interface QueryBuilderFacadeContract
{
    public function select(string $table): QueryBuilderContract;

    public function insert(string $table): QueryBuilderContract;

    public function update(string $table): QueryBuilderContract;

    public function delete(string $table): QueryBuilderContract;
}
