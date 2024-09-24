<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Query\Builders;

use DJWeb\Framework\Base\Application;
use DJWeb\Framework\DBAL\Contracts\ConnectionContract;
use DJWeb\Framework\DBAL\Contracts\Query\QueryBuilderFacadeContract;

class QueryBuilder implements QueryBuilderFacadeContract
{
    public function __construct(private ConnectionContract $connection)
    {
    }

    public function insert(string $table): InsertQueryBuilder
    {
        /** @var InsertQueryBuilder $item */
        $item = Application::getInstance()->get(InsertQueryBuilder::class);
        return $item->table($table);
    }

    public function update(string $table): UpdateQueryBuilder
    {
        /** @var UpdateQueryBuilder $item */
        $item = Application::getInstance()->get(UpdateQueryBuilder::class);
        return $item->table($table);
    }

    public function delete(string $table): DeleteQueryBuilder
    {
        /** @var DeleteQueryBuilder $item */
        $item = Application::getInstance()->get(DeleteQueryBuilder::class);
        return $item->table($table);
    }

    public function select(string $table): SelectQueryBuilder
    {
        /** @var SelectQueryBuilder $item */
        $item = Application::getInstance()->get(SelectQueryBuilder::class);
        return $item->table($table);
    }
}
