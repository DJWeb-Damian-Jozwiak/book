<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Query\Decorators;

class RightJoinDecorator extends JoinDecorator
{
    public string $joinType {
        get => 'RIGHT JOIN';
    }
}
