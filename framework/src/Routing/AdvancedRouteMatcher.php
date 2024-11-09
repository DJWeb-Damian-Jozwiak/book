<?php

declare(strict_types=1);

namespace DJWeb\Framework\Routing;

class AdvancedRouteMatcher extends RouteMatcher
{
    protected function matchesPath(string $path, Route $route): bool
    {
        $path = $this->normalizePath($path);
        $pattern = $this->buildPatternFromPath($route->path);
        $matches = [];

        if (! preg_match($pattern, $path, $matches)) {
            return parent::matchesPath($path, $route);
        }

        $parameters = array_map(
            static fn (RouteParameter $definition) => $definition->getValue($matches),
            $route->parameterDefinitions
        );
        $route->withParameters($parameters);
        return true;
    }
    private function buildPatternFromPath(string $path): string
    {
        $pattern = preg_quote($path, '/');

        // Zamiana <param> na named capture groups
        $pattern = preg_replace('/\\\<([^:>]+)\\\>/', '(?P<$1>[^/]+)', $pattern);

        // Zamiana <param:pattern> na named capture groups z pattern
        $pattern = preg_replace('/\\\<([^:>]+):([^>]+)\\\>/', '(?P<$1>$2)', $pattern);

        // Obsługa opcjonalnych parametrów
        $pattern = preg_replace('/\\\<([^:>]+\?):([^>]+)\\\>/', '(?P<$1>$2)?', $pattern);
        $pattern = preg_replace('/\\\<([^:>]+\?)\\\>/', '(?P<$1>[^/]+)?', $pattern);

        return '/^' . $pattern . '$/';
    }

    private function normalizePath(string $path): string
    {
        // Usuń trailing slash
        $path = rtrim($path, '/');

        // Zamień multiple slashes na pojedynczy
        $path = preg_replace('#/+#', '/', $path);

        // Dodaj leading slash jeśli nie ma
        if (! str_starts_with($path, '/')) {
            $path = '/' . $path;
        }

        return $path;
    }
}
