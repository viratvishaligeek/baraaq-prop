<?php

namespace Database\Seeders;

use App\Models\Homepage;
use Illuminate\Database\Seeder;

class HomepageTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $siteName = trim((string) env('SITE_NAME', 'Buraaqh Properties')) ?: 'Buraaqh Properties Real Estate';

        $homepage = Homepage::query()->firstOrCreate(
            ['slug' => 'home-9'],
            [
                'name' => 'Home 9 — Buraaqh Properties Single Property',
                'status' => 'published',
                'published_at' => now(),
                'meta_title' => 'Discover Most Suitable Property | '.$siteName,
                'meta_description' => 'Explore property information and share your requirements with '.$siteName.' in Bareilly, Uttar Pradesh.',
            ]
        );

    }
}
