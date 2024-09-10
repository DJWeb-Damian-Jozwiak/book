<?php

namespace Tests\Helpers;

class ClassWithDefaultValue
{
    public function __construct(public string $param = 'default_value') {}
}