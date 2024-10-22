<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Models;

use Faker\Factory as FakerFactory;
use Faker\Generator;

abstract class Factory
{
    public function __construct(protected ?Generator $faker = null)
    {
        $this->faker ??= FakerFactory::create();
    }

    /**
     * @param array<string, mixed> $attributes
     *
     * @return Model
     */
    public function make(array $attributes = []): Model
    {
        $modelClass = $this->getModelClass();
        $model = new $modelClass();
        $data = [...$this->definition(), ...$attributes];
        $model->fill($data);
        return $model;
    }

    /**
     * @param array<string, mixed> $attributes
     *
     * @return Model
     */
    public function create(array $attributes = []): Model
    {
        $model = $this->make($attributes);
        $model->save();
        return $model;
    }

    /**
     * @param int $count
     * @param array<string, mixed> $attributes
     *
     * @return array<int, Model>
     */
    public function createMany(int $count, array $attributes = []): array
    {
        $models = [];
        foreach (range(1, $count) as $i) {
            $models[] = $this->create($attributes);
        }
        return $models;
    }

    /**
     * @return array<string, string>
     */
    abstract public function definition(): array;
    /**
     * @return class-string<Model>
     */
    abstract protected function getModelClass(): string;
}
