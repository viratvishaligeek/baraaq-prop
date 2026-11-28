<?php

namespace Database\Seeders;

use App\Models\CircleRateDistrict;
use App\Models\CircleRateTehsil;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CircleRateReferenceSeeder extends Seeder
{
    public function run(): void
    {
        // Administrative names verified against the Bareilly district directory:
        // https://bareilly.nic.in/tehsil/
        // This seeds no circle rates, valuation amounts, or property assumptions.
        // firstOrCreate intentionally preserves administrator edits on later seeder runs.
        $district = CircleRateDistrict::query()->firstOrCreate(
            ['slug' => 'bareilly'],
            ['name' => 'Bareilly', 'state' => 'Uttar Pradesh', 'is_active' => true]
        );

        foreach (['Bareilly', 'Aonla', 'Bahedi', 'Fareedpur', 'Meerganj', 'Nawabganj'] as $name) {
            CircleRateTehsil::query()->firstOrCreate(
                ['district_id' => $district->id, 'slug' => Str::slug($name)],
                ['name' => $name, 'is_active' => true]
            );
        }
    }
}
