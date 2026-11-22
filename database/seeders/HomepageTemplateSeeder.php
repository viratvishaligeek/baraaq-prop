<?php

namespace Database\Seeders;

use App\Models\Homepage;
use Illuminate\Database\Seeder;

class HomepageTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $siteName = trim((string) env('SITE_NAME', 'Pillar Real Estate')) ?: 'Pillar Real Estate';

        $homepage = Homepage::query()->firstOrCreate(
            ['slug' => 'home-9'],
            [
                'name' => 'Home 9 — Pillar Single Property',
                'design_key' => 'home-9',
                'status' => 'published',
                'is_default' => true,
                'is_composition_complete' => false,
                'published_at' => now(),
                'meta_title' => 'Discover Most Suitable Property | '.$siteName,
                'meta_description' => 'Explore thoughtfully selected properties and expert real estate services with '.$siteName.'.',
            ]
        );

        Homepage::query()
            ->where('id', '<>', $homepage->id)
            ->update(['is_default' => false]);

        if ($homepage->status !== 'published') {
            $homepage->update([
                'status' => 'published',
                'published_at' => $homepage->published_at ?: now(),
            ]);
        }

        if (! $homepage->is_default) {
            $homepage->update(['is_default' => true]);
        }
    }
}
