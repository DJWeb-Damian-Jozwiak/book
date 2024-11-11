<?php

declare(strict_types=1);

namespace App\Database\Factories;

use DJWeb\Framework\DBAL\Models\Factory;

class CategoryFactory extends Factory
{
    protected function getModelClass(): string
    {
        return \App\Database\Models\Category::class;
    }

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->realText(),

        ];
    }
};
