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

    /**
     * @param Route $route
     *
     * @return array<string, mixed>
     */
    public function resolveBindings(Route $route): array
    {
        $boundParameters = array_map(static fn ($parameterValue) => $parameterValue, $route->parameters);
        $bindings = $route->bindings;
        $parameters = array_intersect_key($route->parameters, $route->bindings);
        foreach ($parameters as $name => $value) {
            /** @var RouteBinding $binding */
            $binding = $bindings[$name];
            $model = $this->resolveModel($binding, $value);

            $this->checkIfModelExist($model, $binding, $value);

            if (! $binding->validCondition($model)) {
                throw new ModelNotFoundError(
                    "Model {$binding->modelClass} with identifier {$value} did not satisfy conditions"
                );
            }

            $boundParameters[$name] = $model;
        }
        return $boundParameters;
    }

    public function checkIfModelExist(?object $model, RouteBinding $binding, mixed $value): void
    {
        if ($model === null) {
            throw new ModelNotFoundError(
                "Model {$binding->modelClass} with identifier {$value} not found"
            );
        }
    }

    private function resolveModel(RouteBinding $binding, mixed $value): ?object
    {
        $modelClass = $binding->modelClass;

        $model = $this->container->get($modelClass);
        return $model->{$binding->findMethod}($value);
    }
}
