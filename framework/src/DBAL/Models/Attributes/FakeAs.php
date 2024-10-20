<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Models\Attributes;

use Attribute;
use DJWeb\Framework\Enums\FakerMethod;

#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class FakeAs
{
    public function __construct(public FakerMethod $method)
    {
    }

}
