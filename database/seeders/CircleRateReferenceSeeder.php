<?php

namespace Database\Seeders;

use App\Models\CircleRateDistrict;
use App\Models\CircleRateAreaType;
use App\Models\CircleRateLocationLevel;
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

        foreach (['Urban', 'Rural'] as $name) {
            CircleRateAreaType::query()->firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'is_active' => true]
            );
        }

        foreach (['Sub Area', 'Village', 'Mohalla', 'Ward', 'Pargana', 'Locality', 'Zone'] as $index => $name) {
            CircleRateLocationLevel::query()->firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'is_required' => false, 'is_active' => true, 'sort_order' => $index + 1]
            );
        }

        foreach (['Bareilly', 'Aonla', 'Bahedi', 'Fareedpur', 'Meerganj', 'Nawabganj'] as $name) {
            CircleRateTehsil::query()->firstOrCreate(
                ['district_id' => $district->id, 'slug' => Str::slug($name)],
                ['name' => $name, 'is_active' => true]
            );
        }
    }
}
