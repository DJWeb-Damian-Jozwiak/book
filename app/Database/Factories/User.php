<?php

declare(strict_types=1);

namespace App\Database\Factories;

use DJWeb\Framework\DBAL\Models\Factory;

class User extends Factory
{
    protected function getModelClass(): string
    {
        return \DJWeb\Framework\DBAL\Models\Entities\User::class;
    }

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'password' => $this->faker->password(),
            'created_at' => $this->faker->date(),
        ];
    }
};
