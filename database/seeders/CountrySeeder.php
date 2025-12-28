<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('countries')->upsert([
            ['iso2' => 'DE', 'name' => 'Germany',     'te_name' => 'Germany'],
            ['iso2' => 'PL', 'name' => 'Poland',      'te_name' => 'Poland'],
            ['iso2' => 'CZ', 'name' => 'Czechia',     'te_name' => 'Czech Republic'],
            ['iso2' => 'AT', 'name' => 'Austria',     'te_name' => 'Austria'],
            ['iso2' => 'FR', 'name' => 'France',      'te_name' => 'France'],
            ['iso2' => 'BE', 'name' => 'Belgium',     'te_name' => 'Belgium'],
            ['iso2' => 'NL', 'name' => 'Netherlands', 'te_name' => 'Netherlands'],
        ], ['iso2'], ['name', 'te_name']);
    }
}
