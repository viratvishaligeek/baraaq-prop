<?php

namespace Database\Seeders;

use App\Models\Homepage;
use Illuminate\Database\Seeder;

class HomepageTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $siteName = trim((string) env('SITE_NAME', 'Buraaqh Properties')) ?: 'Buraaqh Properties';
        if (stripos($siteName, 'Pillar') !== false) $siteName = 'Buraaqh Properties';

        $homepage = Homepage::query()->firstOrCreate(
            ['slug' => 'home-9'],
            [
                'name' => 'Home 9 — Buraaqh Properties Home 9',
                'status' => 'published',
                'published_at' => now(),
                'meta_title' => 'Discover Most Suitable Property | '.$siteName,
                'meta_description' => 'Explore thoughtfully selected properties and expert real estate services with '.$siteName.'.',
            ]
        );

        if ($homepage->status !== 'published' || ! $homepage->published_at) {
            $homepage->update([
                'status' => 'published',
                'published_at' => $homepage->published_at ?: now(),
            ]);
        }
    }
}
