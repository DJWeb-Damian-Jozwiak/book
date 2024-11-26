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
        $this->schema->createTable('mail_history', [
            new IntColumn('id', autoIncrement: true, nullable: false),
            new VarcharColumn('from_email', length: 255),
            new VarcharColumn('from_name', length: 255),
            new VarcharColumn('subject', length: 255),
            new VarcharColumn('to_email', length: 255),
            new VarcharColumn('to_name', length: 255),
            new VarcharColumn('cc_email', nullable: true, length: 255),
            new VarcharColumn('bcc_email', nullable: true, length: 255),
            new VarcharColumn('reply_to_email', nullable: true, length: 255),
            new VarcharColumn('status', length: 20),
            new TextColumn('error', nullable: true),
            new DateTimeColumn('created_at', current: true),
            new DateTimeColumn('updated_at', currentOnUpdate: true),
            new PrimaryColumn('id'),
        ]);

        $this->schema->createIndex('mail_history', 'mail_history_status_index', ['status']);
        $this->schema->createIndex('mail_history', 'mail_history_created_at_index', ['created_at']);
    }

    public function down(): void
    {
        $this->schema->dropTable('mail_history');
    }
};