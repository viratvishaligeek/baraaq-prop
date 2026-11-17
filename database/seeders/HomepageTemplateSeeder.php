<?php

namespace Database\Seeders;

use App\Models\Homepage;
use Illuminate\Database\Seeder;

class HomepageTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            ['design_key' => 'index', 'name' => 'Home 1 — Real Estate'],
            ['design_key' => 'home-2', 'name' => 'Home 2 — Property'],
            ['design_key' => 'home-3', 'name' => 'Home 3 — Map'],
            ['design_key' => 'home-4', 'name' => 'Home 4 — Luxury'],
            ['design_key' => 'home-5', 'name' => 'Home 5 — Single Property'],
            ['design_key' => 'home-6', 'name' => 'Home 6 — Real Estate Company'],
            ['design_key' => 'home-7', 'name' => 'Home 7 — Real Estate Agency'],
            ['design_key' => 'home-8', 'name' => 'Home 8 — Local Agency'],
            ['design_key' => 'home-9', 'name' => 'Home 9 — Single Property V2'],
            ['design_key' => 'home-10', 'name' => 'Home 10 — Modern House Agency'],
        ];

        foreach ($templates as $template) {
            Homepage::firstOrCreate(
                ['slug' => $template['design_key']],
                [
                    'name' => $template['name'],
                    'design_key' => $template['design_key'],
                    'status' => 'published',
                    'is_default' => $template['design_key'] === 'index',
                    'published_at' => now(),
                ]
            );
        }
    }
}
