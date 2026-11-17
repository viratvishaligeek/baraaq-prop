<?php

namespace App\Services\Homepage;

class HomepagePresetSectionRegistry
{
    /**
     * Return the original source order for a template, including its hero.
     */
    public function forDesign(string $designKey): array
    {
        return array_values(array_merge(
            ['hero.' . $designKey],
            config('cms-homepage-presets.' . $designKey, [])
        ));
    }
}
