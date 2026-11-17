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
