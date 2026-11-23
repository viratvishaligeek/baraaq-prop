<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class BuraaqhContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            BuraaqhLegacyDemoContentRetirementSeeder::class,
            BuraaqhCategorySeeder::class,
            BuraaqhPageSeeder::class,
            BuraaqhEditorialSeeder::class,
            BuraaqhServiceSeeder::class,
            BuraaqhNavigationSeeder::class,
            BuraaqhHomepageSeeder::class,
        ]);

        Cache::forget('homepage:published-default');
        Cache::forget('homepage:published-default:id');
        Cache::forget('settings:all');
    }
}
