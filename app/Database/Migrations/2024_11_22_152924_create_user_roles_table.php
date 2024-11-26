<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use DJWeb\Framework\DBAL\Migrations\Migration;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\DateTimeColumn;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\IntColumn;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\PrimaryColumn;

return new class extends Migration
{
    /**
     * run migration.
     */
    public function up(): void
    {
        $this->schema->createTable('user_roles', [
            new IntColumn('id', nullable: false, autoIncrement: true),
            new DateTimeColumn('created_at', current: true),
            new IntColumn('user_id', nullable: false),
            new IntColumn('role_id', nullable: false),
            new PrimaryColumn('id'),
        ]);

        $this->schema->uniqueIndex('user_roles', 'unique_user_roles', ['user_id', 'role_id']);
    }

    /**
     * rollback migration.
     */
    public function down(): void
    {
        $this->schema->dropTable('user_roles');
    }
};