<?php

namespace Tests\Helpers;


class ClassWithDependency
{
    public function __construct(public SimpleClass $dependency) {}
}