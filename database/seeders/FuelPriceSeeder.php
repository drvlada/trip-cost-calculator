<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FuelPriceSeeder extends Seeder
{
    public function run(): void
    {
        // Assumes countries and fuel_types already seeded
        $prices = [
            // Germany
            ['country_iso2' => 'DE', 'fuel_code' => 'gasoline', 'price' => 1.80],
            ['country_iso2' => 'DE', 'fuel_code' => 'diesel',   'price' => 1.70],

            // Poland
            ['country_iso2' => 'PL', 'fuel_code' => 'gasoline', 'price' => 1.55],
            ['country_iso2' => 'PL', 'fuel_code' => 'diesel',   'price' => 1.45],

            // France
            ['country_iso2' => 'FR', 'fuel_code' => 'gasoline', 'price' => 1.85],
            ['country_iso2' => 'FR', 'fuel_code' => 'diesel',   'price' => 1.75],
        ];

        foreach ($prices as $row) {
            DB::table('fuel_prices')->insert([
                'country_id' => DB::table('countries')->where('iso2', $row['country_iso2'])->value('id'),
                'fuel_type_id' => DB::table('fuel_types')->where('code', $row['fuel_code'])->value('id'),
                'price_per_liter' => $row['price'],
                'currency' => 'EUR',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
