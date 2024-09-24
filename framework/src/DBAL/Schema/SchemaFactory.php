<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Schema;

use DJWeb\Framework\DBAL\Contracts\ConnectionContract;
use DJWeb\Framework\DBAL\Contracts\Schema\SchemaContract;
use DJWeb\Framework\DBAL\Schema\MySQL\Managers\ColumnFactory;
use DJWeb\Framework\DBAL\Schema\MySQL\Managers\ColumnManager;
use DJWeb\Framework\DBAL\Schema\MySQL\Managers\DatabaseInfo;
use DJWeb\Framework\DBAL\Schema\MySQL\Managers\IndexManager;
use DJWeb\Framework\DBAL\Schema\MySQL\Managers\TableManager;
use DJWeb\Framework\DBAL\Schema\MySQL\Schema;
use DJWeb\Framework\DBAL\Schema\MySQL\Transaction;

class SchemaFactory
{
    public static function create(
        ConnectionContract $connection
    ): SchemaContract {
        $tableManager = new TableManager($connection);
        $columnManager = new ColumnManager($connection);
        $indexManager = new IndexManager($connection);
        $databaseInfo = new DatabaseInfo($connection);
        $columnFactory = new ColumnFactory();
        return new Schema(
            $tableManager,
            $columnManager,
            $indexManager,
            $databaseInfo,
            $columnFactory,
            new Transaction($connection),
            $connection
        );
    }
}
