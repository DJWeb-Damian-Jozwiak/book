<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use DJWeb\Framework\DBAL\Migrations\Migration;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\DateTimeColumn;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\IntColumn;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\PrimaryColumn;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\VarcharColumn;

return new class extends Migration
{
    /**
     * run migration.
     */
    public function up(): void
    {
        $this->schema->createTable('users', [
            new IntColumn('id', nullable: false, autoIncrement: true),
            new VarcharColumn('name'),
            new VarcharColumn('email'),
            new VarcharColumn('password'),
            new DateTimeColumn('created_at', current: true),
            new DateTimeColumn('updated_at', currentOnUpdate: true),
            new PrimaryColumn('id'),
        ]);
        $this->schema->uniqueIndex('users', 'unique_users_email', 'email');
    }

    /**
     * rollback migration.
     */
    public function down(): void
    {
        $this->schema->dropTable('users');
    }
};