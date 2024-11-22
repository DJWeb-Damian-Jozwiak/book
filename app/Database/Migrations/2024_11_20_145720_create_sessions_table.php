<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use DJWeb\Framework\DBAL\Migrations\Migration;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\DateTimeColumn;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\IntColumn;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\PrimaryColumn;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\TextColumn;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\VarcharColumn;

return new class extends Migration
{
    /**
     * run migration.
     */
    public function up(): void
    {
        $this->schema->createTable('sessions', [
            new VarcharColumn('id', nullable: false, length: 128),
            new TextColumn('payload', nullable: true),
            new IntColumn('last_activity', nullable: true),
            new VarcharColumn('user_ip', nullable: true, length: 45),
            new VarcharColumn('user_agent', nullable: true, length: 255),
            new IntColumn('user_id', nullable: true),
            new DateTimeColumn('created_at', current: true),
            new DateTimeColumn('updated_at', currentOnUpdate: true),
            new PrimaryColumn('id'),
        ]);
        $this->schema->createIndex('sessions', 'sessions_last_activity_index', ['last_activity']);
        $this->schema->createIndex('sessions', 'sessions_user_id_index', ['user_id']);
    }

    /**
     * rollback migration.
     */
    public function down(): void
    {
        $this->schema->dropTable('sessions');
    }
};