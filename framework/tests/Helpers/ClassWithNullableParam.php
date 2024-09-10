<?php

namespace Tests\Helpers;

class ClassWithNullableParam
{
    public function __construct(public ?string $param) {}
}