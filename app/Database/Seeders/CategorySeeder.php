<?php

declare(strict_types=1);

namespace App\Database\Seeders;

use App\Database\Factories\CategoryFactory;
use DJWeb\Framework\DBAL\Models\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        new CategoryFactory()->createMany(25);
    }
};
