<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use DJWeb\Framework\DBAL\Migrations\Migration;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\DateTimeColumn;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\IntColumn;

return new class extends Migration
{
    /**
     * run migration.
     */
    public function up(): void
    {
        $this->schema->createTable('role_permissions', [
            new IntColumn('role_id'),
            new IntColumn('permission_id'),
            new DateTimeColumn('created_at', current: true),
        ]);
        $this->schema->uniqueIndex(
            'role_permissions',
            'unique_role_permissions',
            ['role_id', 'permission_id']
        );
    }

    /**
     * rollback migration.
     */
    public function down(): void
    {
        $this->schema->dropTable('role_permissions');
    }
};