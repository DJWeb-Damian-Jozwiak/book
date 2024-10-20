<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Models\Contracts;

use DJWeb\Framework\DBAL\Models\Model;

interface RelationContract
{
    public function addConstraints(): void;
    /**
     * @return array<int, Model>|Model
     */
    public function getResults(): array|Model;

}
