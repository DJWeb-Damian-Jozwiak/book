<?php

namespace Tests\Helpers;

class ClassWithBuiltInTypes
{
    public function __construct(
        public int $intParam,
        public float $floatParam,
        public string $stringParam,
        public bool $boolParam,
        public array $arrayParam
    ) {}
}