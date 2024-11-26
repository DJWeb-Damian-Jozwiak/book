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
            new VarcharColumn('email', length: 255),
            new VarcharColumn('username', length: 100),
            new VarcharColumn('password', length: 255),
            new VarcharColumn('remember_token', length: 100, nullable: true),
            new VarcharColumn('password_reset_token', length: 100, nullable: true),
            new DateTimeColumn('password_reset_expires', nullable: true),
            new DateTimeColumn('email_verified_at', nullable: true),
            new IntColumn('is_active', default: 1),
            new DateTimeColumn('last_login_at', nullable: true),
            new DateTimeColumn('created_at', current: true),
            new DateTimeColumn('updated_at', currentOnUpdate: true),
            new PrimaryColumn('id'),
        ]);

        $this->schema->uniqueIndex('users', 'unique_users_email', 'email');
        $this->schema->uniqueIndex('users', 'unique_users_username', 'username');

    }

    /**
     * rollback migration.
     */
    public function down(): void
    {
        $this->schema->dropTable('users');
    }
};