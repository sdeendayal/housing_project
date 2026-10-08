<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ews_town_seeder extends Seeder
{
    public function run(): void
    {
        $this->call(EwsTownSeeder::class);
    }
}
