<?php

namespace DJWeb\Framework\DBAL\Contracts\Query;

interface DeleteQueryBuilderContract extends QueryBuilderContract
{
    public function delete(): bool;
}