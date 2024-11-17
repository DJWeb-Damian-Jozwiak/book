<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Query\Builders;

use DJWeb\Framework\Base\Application;
use DJWeb\Framework\DBAL\Contracts\Query\DeleteQueryBuilderContract;
use DJWeb\Framework\DBAL\Contracts\Query\InsertQueryBuilderContract;
use DJWeb\Framework\DBAL\Contracts\Query\QueryBuilderFacadeContract;
use DJWeb\Framework\DBAL\Contracts\Query\SelectQueryBuilderContract;
use DJWeb\Framework\DBAL\Contracts\Query\UpdateQueryBuilderContract;

class QueryBuilder implements QueryBuilderFacadeContract
{
    public function insert(string $table): InsertQueryBuilderContract
    {
        /** @var InsertQueryBuilderContract $item */
        $item = Application::getInstance()->get(InsertQueryBuilderContract::class);
        return $item->table($table);
    }

    public function update(string $table): UpdateQueryBuilderContract
    {
        /** @var UpdateQueryBuilderContract $item */
        $item = Application::getInstance()->get(UpdateQueryBuilderContract::class);
        return $item->table($table);
    }

    public function delete(string $table): DeleteQueryBuilderContract
    {
        /** @var DeleteQueryBuilderContract $item */
        $item = Application::getInstance()->get(DeleteQueryBuilderContract::class);
        return $item->table($table);
    }

    public function select(string $table): SelectQueryBuilderContract
    {
        /** @var SelectQueryBuilderContract $item */
        $item = Application::getInstance()->get(SelectQueryBuilderContract::class);
        return $item->table($table);
    }
}
