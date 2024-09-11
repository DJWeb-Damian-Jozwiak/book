<?php

function env(string $key, mixed $value = null): mixed
{
    return $_ENV[$key] ?? $value;
}