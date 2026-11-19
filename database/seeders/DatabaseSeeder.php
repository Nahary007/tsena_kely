<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // Execution des seeders du projet
    public function run(): void
    {
        $this->call([
            ProductSeeder::class,
        ]);
    }
}
