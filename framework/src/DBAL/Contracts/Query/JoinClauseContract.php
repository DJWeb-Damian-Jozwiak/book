<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Contracts\Query;

interface JoinClauseContract
{
    public function getSQL(): string;
}
