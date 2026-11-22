<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        foreach ([
            'footer-1' => ['Footer section 1', 'Dynamic content for the first original footer column.'],
            'footer-2' => ['Footer section 2', 'Dynamic content for the second original footer column.'],
            'footer-3' => ['Footer section 3', 'Dynamic content for the third original footer column.'],
            'footer-4' => ['Footer section 4', 'Dynamic content for the fourth original footer column.'],
        ] as $location => [$name, $description]) {
            // Seed only missing defaults. Never overwrite a site owner's existing widget area.
            if (DB::table('widget_areas')->where('location', $location)->exists()) {
                continue;
            }

            DB::table('widget_areas')->insert([
                'name' => $name,
                'location' => $location,
                'description' => $description,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        // Footer areas may have been customized after migration; preserve them on rollback.
    }
};
