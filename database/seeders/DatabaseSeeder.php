<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed reference tables first
        $this->call([
            FuelTypeSeeder::class,
            CountrySeeder::class,
            FuelPriceSeeder::class,
        ]);

        // Create a test user (optional, for development)
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
