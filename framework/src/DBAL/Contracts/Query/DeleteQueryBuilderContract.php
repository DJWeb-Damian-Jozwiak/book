<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Contracts\Query;

interface DeleteQueryBuilderContract extends QueryBuilderContract
{
    public function delete(): bool;

}
