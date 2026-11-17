<?php

namespace Database\Seeders;

use App\Models\WidgetArea;
use Illuminate\Database\Seeder;

class WidgetAreaSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'header' => ['Header', 'Widgets displayed in the site header.'],
            'footer' => ['Footer', 'Widgets displayed in the site footer.'],
            'footer-1' => ['Footer section 1', 'Dynamic content for the first original footer column.'],
            'footer-2' => ['Footer section 2', 'Dynamic content for the second original footer column.'],
            'footer-3' => ['Footer section 3', 'Dynamic content for the third original footer column.'],
            'footer-4' => ['Footer section 4', 'Dynamic content for the fourth original footer column.'],
            'sidebar' => ['Sidebar', 'Widgets displayed in content sidebars.'],
            'homepage' => ['Homepage', 'Widgets displayed in homepage content.'],
        ] as $location => [$name, $description]) {
            WidgetArea::updateOrCreate(
                ['location' => $location],
                ['name' => $name, 'description' => $description, 'is_active' => true]
            );
        }
    }
}
