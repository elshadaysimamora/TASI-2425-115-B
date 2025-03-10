<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RatingConfiguration;

class RatingConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
                // Membuat beberapa konfigurasi rating default
                RatingConfiguration::create([
                    'name' => 'Default Rating',
                    'min_scale' => 1,
                    'max_scale' => 5,
                    'is_active' => true,
                ]);
        
                RatingConfiguration::create([
                    'name' => 'Basic Rating',
                    'min_scale' => 1,
                    'max_scale' => 3,
                    'is_active' => true,
                ]);
        
                RatingConfiguration::create([
                    'name' => 'Advanced Rating',
                    'min_scale' => 1,
                    'max_scale' => 10,
                    'is_active' => false,
                ]);
    }
}
