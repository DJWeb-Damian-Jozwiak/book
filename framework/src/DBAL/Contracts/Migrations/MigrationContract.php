<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Contracts\Migrations;

interface MigrationContract
{
    public function up(): void;

    public function down(): void;
}
