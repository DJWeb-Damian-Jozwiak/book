<?php

declare(strict_types=1);

namespace App\Database\Seeders;

use App\Database\Factories\User as UserFactory;
use DJWeb\Framework\DBAL\Models\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        new UserFactory()->createMany(25);
    }
};
