<?php

declare(strict_types=1);

namespace App\Database\Seeders;

use DJWeb\Framework\DBAL\Models\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CategorySeeder::class);
    }
};
