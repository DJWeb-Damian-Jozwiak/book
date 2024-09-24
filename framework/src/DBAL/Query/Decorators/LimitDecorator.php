<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Query\Decorators;

use DJWeb\Framework\DBAL\Contracts\Query\LimitDecoratorContract;

class LimitDecorator implements LimitDecoratorContract
{
    public function __construct(
        private ?int $limit = null,
        private ?int $offset = null
    ) {
    }

    public function limit(int $limit): LimitDecoratorContract
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): LimitDecoratorContract
    {
        $this->offset = $offset;
        return $this;
    }

    public function getSql(): string
    {
        $sql = '';
        if ($this->limit !== null) {
            $sql = trim($sql);
            $sql .= "LIMIT {$this->limit}";
        }

        if ($this->offset !== null) {
            $sql = trim($sql);
            $sql .= " OFFSET {$this->offset}";
        }
        return trim($sql);
    }
}