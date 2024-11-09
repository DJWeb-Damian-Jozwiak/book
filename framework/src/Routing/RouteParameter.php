<?php

declare(strict_types=1);

namespace DJWeb\Framework\Routing;

readonly class RouteParameter
{
    final public function __construct(
        public string $name,
        public string $pattern = '[^/]+',
        public bool $optional = false,
        public mixed $defaultValue = null
    ) {
    }

    /**
     * @param array<string, mixed> $matches
     *
     * @return mixed
     */
    public function getValue(array $matches): mixed
    {
       if (isset($matches[$this->name])) {
           return $matches[$this->name];
       }
       if($this->optional) {
           return $this->defaultValue;
       }
       throw new \RuntimeException("Parameter {$this->name} not found");
    }
}
