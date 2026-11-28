<?php

namespace Database\Seeders;

use App\Models\CircleRateDistrict;
use App\Models\CircleRateAreaType;
use App\Models\CircleRateLocationLevel;
use App\Models\CircleRateLocation;
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

        foreach (['Sub Area', 'Village', 'Mohalla', 'Ward', 'Pargana', 'Locality', 'Zone', 'Development Block'] as $index => $name) {
            CircleRateLocationLevel::query()->firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'is_required' => false, 'is_active' => true, 'sort_order' => $index + 1]
            );
        }

        $blockLevel = CircleRateLocationLevel::query()->where('slug', 'development-block')->first();
        // The official district directory lists 15 blocks. Their tehsil mapping is intentionally left
        // unset because the source page does not provide that mapping; administrators can configure it.
        foreach (['Alampur Zafarabad', 'Bahedi', 'Bhadpura', 'Bhojipura', 'Bhuta', 'Bithri Chainpur', 'Fareedpur', 'Fatehganj West', 'Kyara', 'Majhgawa', 'Meerganj', 'Nawabganj', 'Ramnagar', 'Richha', 'Shergarh'] as $name) {
            CircleRateLocation::query()->firstOrCreate(
                ['district_id' => $district->id, 'slug' => 'block-'.Str::slug($name)],
                ['name' => $name, 'level_id' => $blockLevel?->id, 'is_active' => true]
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
