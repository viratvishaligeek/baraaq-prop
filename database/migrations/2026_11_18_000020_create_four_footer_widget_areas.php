<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Support\\Facades\\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = date('Y-m-d H:i:s');

        foreach ([
            'footer-1' => ['Footer section 1', 'Dynamic content for the first original footer column.'],
            'footer-2' => ['Footer section 2', 'Dynamic content for the second original footer column.'],
            'footer-3' => ['Footer section 3', 'Dynamic content for the third original footer column.'],
            'footer-4' => ['Footer section 4', 'Dynamic content for the fourth original footer column.'],
        ] as $location => [$name, $description]) {
            $values = [
                'name' => $name,
                'description' => $description,
                'is_active' => true,
                'updated_at' => $now,
            ];

            if (DB::table('widget_areas')->where('location', $location)->exists()) {
                DB::table('widget_areas')->where('location', $location)->update($values);
            } else {
                DB::table('widget_areas')->insert($values + [
                    'location' => $location,
                    'created_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('widget_areas')->whereIn('location', [
            'footer-1', 'footer-2', 'footer-3', 'footer-4',
        ])->delete();
    }
};
