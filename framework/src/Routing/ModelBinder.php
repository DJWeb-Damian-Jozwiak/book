<?php

declare(strict_types=1);

namespace DJWeb\Framework\Routing;

use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\Exceptions\DBAL\ModelNotFoundError;
use DJWeb\Framework\Routing\Contracts\ModelBinderContract;

readonly class ModelBinder implements ModelBinderContract
{
    public function __construct(
        private ContainerContract $container,
    ) {
    }
    public function resolveBindings(Route $route): array
    {
        $boundParameters = [];
        $parameters = $route->parameters;
        $bindings = $route->bindings;
        $parameters = array_filter($parameters, static fn (string $name) => isset($bindings[$name]));

        foreach ($parameters as $name => $value) {
            /** @var RouteBinding $binding */
            $binding = $bindings[$name];
            $model = $this->resolveModel($binding, $value);

            if ($model === null) {
                throw new ModelNotFoundError(
                    "Model {$binding->modelClass} with identifier {$value} not found"
                );
            }

            if ($binding->condition && ! ($binding->condition)($model)) {
                throw new ModelNotFoundError(
                    "Model {$binding->modelClass} with identifier {$value} did not satisfy conditions"
                );
            }

            $boundParameters[$name] = $model;
        }
        return $boundParameters;
    }

    private function resolveModel(RouteBinding $binding, mixed $value): ?object
    {
        $modelClass = $binding->modelClass;

        $model = $this->container->get($modelClass);
        return $model->{$binding->findMethod}($value);
    }
}
