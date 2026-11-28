<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Artisan::command('bareilly:seed-demo', function () {
    app(\\Database\\Seeders\\BareillyCityDemoSeeder::class)->run();
    $this->info('Bareilly city DEMO data is seeded. Sample rates remain pending and marked DEMO.');
})->purpose('Seed idempotent Bareilly city-only circle rate demo data');

Artisan::command('bareilly:reset-demo', function () {
    if (! $this->confirm('This deletes ONLY records marked is_demo in the Bareilly circle-rate module. Continue?', false)) {
        $this->warn('Demo reset cancelled.');
        return self::FAILURE;
    }

    $counts = app(\\App\\Services\\CircleRate\\DemoDataManagementService::class)->reset(null);
    $this->table(['Demo record type', 'Deleted before reset'], collect($counts)->map(fn ($count, $label) => [$label, $count])->values()->all());
    $this->info('Demo data reset complete. Official and manually managed records were retained.');
    return self::SUCCESS;
})->purpose('Safely reset only Bareilly circle-rate demo records');
