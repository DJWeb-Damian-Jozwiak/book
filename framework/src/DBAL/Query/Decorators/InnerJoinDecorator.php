<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Query\Decorators;

class InnerJoinDecorator extends JoinDecorator
{
    protected function joinType(): string
    {
        return 'INNER JOIN';
    }
}
