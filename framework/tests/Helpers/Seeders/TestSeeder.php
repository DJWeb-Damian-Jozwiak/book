<?php

namespace Tests\Helpers\Seeders;

use DJWeb\Framework\DBAL\Models\Seeder;

class TestSeeder extends Seeder
{
    public function run(): void
    {
       $this->call(SecondSeeder::class);
    }
}