@extends('admin.layouts.app')
@section('title', 'Calculator Settings')
@section('content')
    <div class="mb-4"><h1 class="h3 mb-1">Calculator & Calculation Settings</h1><p class="text-muted mb-0">Control public calculator availability, comparison display and safety limits.</p></div>
    <form method="POST" action="{{ route('admin.circle-rates.settings.update') }}" class="card border-0 shadow-sm p-3 p-md-4">@csrf @method('PUT')
        @foreach([
            'calculator_enabled' => 'Enable public calculator',
            'show_previous_year_comparison' => 'Show previous financial year comparison',
            'allow_public_result_sharing' => 'Allow sharing valuation result links'
        ] as $key => $label)
            <div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" name="{{ $key }}" value="1" id="{{ $key }}" @checked($settings[$key])><label class="form-check-label" for="{{ $key }}">{{ $label }}</label></div>
        @endforeach
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Maximum property area accepted</label><input class="form-control" type="number" min="0.000001" step="0.000001" name="max_property_area" value="{{ $settings['max_property_area'] }}" required><small class="text-muted">A safety limit only; this does not change government valuation rules.</small></div>
            <div class="col-md-6"><label class="form-label">Rate cache lifetime (minutes)</label><input class="form-control" type="number" min="0" max="1440" name="rate_cache_minutes" value="{{ $settings['rate_cache_minutes'] }}" required><small class="text-muted">0 disables rate caching so newly verified rates are reflected immediately.</small></div>
            <div class="col-12"><label class="form-label">Public valuation disclaimer</label><textarea class="form-control" rows="4" name="valuation_disclaimer" required>{{ $settings['valuation_disclaimer'] }}</textarea></div>
        </div>
        <button class="btn btn-primary mt-4">Save settings</button>
    </form>
@endsection
