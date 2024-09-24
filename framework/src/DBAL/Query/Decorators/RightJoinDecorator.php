<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Query\Decorators;

class RightJoinDecorator extends JoinDecorator
{
    protected function joinType(): string
    {
        return 'RIGHT JOIN';
    }
}
