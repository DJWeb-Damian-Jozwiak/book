<?php

namespace tests\Helpers;

class ClassWithUnresolvableDependency
{
    public function __construct(public NonExistentClass $dependency) {}
}