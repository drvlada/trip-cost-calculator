<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FuelTypeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('fuel_types')->upsert([
            ['code' => 'gasoline', 'name' => 'Gasoline', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'diesel',   'name' => 'Diesel',   'created_at' => now(), 'updated_at' => now()],
            ['code' => 'lpg',      'name' => 'LPG',      'created_at' => now(), 'updated_at' => now()],
            ['code' => 'ev',       'name' => 'Electric', 'created_at' => now(), 'updated_at' => now()],
        ], ['code'], ['name', 'updated_at']);
    }
}
