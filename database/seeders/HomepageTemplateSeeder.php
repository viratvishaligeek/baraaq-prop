<?php

namespace Database\Seeders;

use App\Models\Homepage;
use Illuminate\Database\Seeder;

class HomepageTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $existingHomepage = Homepage::withTrashed()->where('slug', 'home-9')->first();

        if ($existingHomepage?->trashed()) {
            return;
        }

        if ($existingHomepage) {
            return;
        }

        Homepage::query()->firstOrCreate(
            ['slug' => 'home-9'],
            [
                'name' => 'Home 9 — Buraaqh Properties',
                'design_key' => 'home-9',
                'is_default' => true,
                'status' => 'published',
                'published_at' => now(),
                'meta_title' => 'Buraaqh Properties | Real Estate in Bareilly, Uttar Pradesh',
                'meta_description' => 'Explore residential and commercial property guidance in Bareilly, Uttar Pradesh. Share your requirements with Buraaqh Properties and verify property details before proceeding.',
            ]
        );
    }
}
