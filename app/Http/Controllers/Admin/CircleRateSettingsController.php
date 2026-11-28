<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\CircleRate\CircleRateCache;
use Illuminate\Http\Request;

class CircleRateSettingsController extends Controller
{
    public function index()
    {
        return view('admin.circle-rates.settings.index', [
            'settings' => [
                'calculator_enabled' => Setting::read('circle_rate.calculator_enabled', true),
                'show_previous_year_comparison' => Setting::read('circle_rate.show_previous_year_comparison', true),
                'allow_public_result_sharing' => Setting::read('circle_rate.allow_public_result_sharing', true),
                'max_property_area' => Setting::read('circle_rate.max_property_area', 100000000),
                'rate_cache_minutes' => Setting::read('circle_rate.rate_cache_minutes', 0),
                'valuation_disclaimer' => Setting::read('circle_rate.valuation_disclaimer', 'This is an estimate based on published, verified circle rate records. It is not a market-price appraisal or a guarantee of the final registration assessment.'),
            ],
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'calculator_enabled' => ['nullable', 'boolean'],
            'show_previous_year_comparison' => ['nullable', 'boolean'],
            'allow_public_result_sharing' => ['nullable', 'boolean'],
            'max_property_area' => ['required', 'numeric', 'gt:0', 'max:100000000000'],
            'rate_cache_minutes' => ['required', 'integer', 'min:0', 'max:1440'],
            'valuation_disclaimer' => ['required', 'string', 'max:2000'],
        ]);

        foreach (['calculator_enabled', 'show_previous_year_comparison', 'allow_public_result_sharing'] as $key) {
            Setting::write('circle_rate.'.$key, $request->boolean($key), 'circle_rate', 'boolean');
        }
        Setting::write('circle_rate.max_property_area', $data['max_property_area'], 'circle_rate', 'string');
        Setting::write('circle_rate.rate_cache_minutes', $data['rate_cache_minutes'], 'circle_rate', 'string');
        Setting::write('circle_rate.valuation_disclaimer', $data['valuation_disclaimer'], 'circle_rate', 'string');
        CircleRateCache::bumpVersion();

        return back()->with('success', 'Circle rate calculator settings saved.');
    }
}
