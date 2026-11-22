<?php

namespace Database\Seeders;

use App\Models\Homepage;
use Illuminate\Database\Seeder;

class HomepageTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $homepage = Homepage::query()->firstOrCreate(
            ['slug' => 'home-9'],
            [
                'name' => 'Home 9 — Pillar Single Property',
                'design_key' => 'home-9',
                'status' => 'published',
                'is_default' => true,
                'is_composition_complete' => false,
                'published_at' => now(),
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
