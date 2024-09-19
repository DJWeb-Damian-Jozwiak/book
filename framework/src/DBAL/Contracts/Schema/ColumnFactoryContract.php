<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Contracts\Schema;

use DJWeb\Framework\DBAL\Schema\Column;

interface ColumnFactoryContract
{
    /**
     * @param array<string, mixed> $description
     */
    public function createFromDescription(array $description): Column;
}
