<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Contracts\Query;

interface LimitDecoratorContract
{
    public function getSql(): string;

    public function limit(int $limit): LimitDecoratorContract;

    public function offset(int $offset): LimitDecoratorContract;
}
