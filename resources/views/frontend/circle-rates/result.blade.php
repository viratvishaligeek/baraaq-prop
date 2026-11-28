@extends('frontend.layouts.theme')

@section('title', 'Property Valuation Result')
@section('meta_description', 'Circle rate valuation estimate and calculation breakdown.')

@section('content')
<section class="space" style="padding: 70px 0;">
    <div class="container">
        <div class="row justify-content-center"><div class="col-lg-9">
            <div class="text-center mb-4">
                <span class="sub-title">Valuation estimate</span>
                <h1 class="h2">Property Circle Rate Result</h1>
                <p class="text-muted">Reference: {{ $record->reference }}</p>
            </div>
            <div class="card border-0 shadow-sm p-4 mb-4">
                <div class="text-center">
                    <div class="text-muted">Estimated government circle-rate value</div>
                    <div class="display-5 fw-bold">₹{{ number_format((float) $record->estimated_value, 2) }}</div>
                    <div class="small text-muted mt-1">Financial year {{ $record->financialYear?->name ?? '—' }}</div>
                </div>
                <hr>
                <div class="row g-3">
                    <div class="col-md-6"><strong>District:</strong> {{ $record->rate?->district?->name ?? '—' }}</div>
                    <div class="col-md-6"><strong>Tehsil:</strong> {{ $record->rate?->tehsil?->name ?? '—' }}</div>
                    <div class="col-md-6"><strong>Location:</strong> {{ $record->rate?->location?->name ?? '—' }}</div>
                    <div class="col-md-6"><strong>Property type:</strong> {{ $record->rate?->propertyType?->name ?? '—' }}</div>
                    <div class="col-md-6"><strong>Category:</strong> {{ $record->rate?->category?->name ?? '—' }}</div>
                    <div class="col-md-6"><strong>Property area:</strong> {{ number_format((float) $record->property_area, 4) }}</div>
                    <div class="col-md-6"><strong>Rate:</strong> {{ number_format((float) $record->applicable_rate, 2) }} / {{ $record->rate?->unit?->name ?? '—' }}</div>
                    <div class="col-md-6"><strong>Effective from:</strong> {{ $record->rate?->effective_from?->format('d M Y') ?? '—' }}</div>
                    <div class="col-md-6"><strong>Verification:</strong> {{ ucfirst($record->rate?->verification_status ?? 'unknown') }}</div>
                    <div class="col-md-6"><strong>Source document:</strong>
                        @if($record->rate?->source?->document_path)
                            <a href="{{ asset('storage/'.$record->rate->source->document_path) }}" target="_blank" rel="noopener">{{ $record->rate->source->title }}</a>
                        @else{{ $record->rate?->source?->title ?? 'Not supplied' }}@endif
                    </div>
                    <div class="col-md-6"><strong>Calculation date:</strong> {{ $record->valued_at?->format('d M Y, H:i') }}</div>
                </div>
            </div>
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h2 class="h5">Calculation breakdown</h2>
                <p class="mb-2"><strong>Formula:</strong> {{ $record->calculation_breakdown['formula'] ?? 'Not available' }}</p>
                @if(!empty($record->calculation_breakdown['applied_conditions']))
                    <div class="small text-muted mb-2">Applied rate conditions</div><pre class="small bg-light p-2 rounded">{{ json_encode($record->calculation_breakdown['applied_conditions'], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre>
                @endif
                @if(!empty($record->calculation_breakdown['applied_rules']))
                    <div class="small text-muted mb-2">Applied verified rules</div>
                    <ul class="mb-0">@foreach($record->calculation_breakdown['applied_rules'] as $rule)<li>{{ $rule['name'] }} — {{ $rule['source'] ?? 'Source not named' }}</li>@endforeach</ul>
                @endif
            </div>
            @if($showPreviousComparison)
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h2 class="h5">Year-on-year comparison</h2>
                @if ($record->previous_year_value !== null)
                    <div class="row g-3">
                        <div class="col-md-4"><div class="text-muted small">Previous-year value</div><strong>₹{{ number_format((float) $record->previous_year_value, 2) }}</strong><div class="small text-muted">{{ $record->previousRate?->financialYear?->name ?? 'Previous year' }} · {{ $record->previousRate?->rate }} / {{ $record->previousRate?->unit?->name }}</div></div>
                        <div class="col-md-4"><div class="text-muted small">Difference</div><strong>₹{{ number_format((float) $record->difference_amount, 2) }}</strong></div>
                        <div class="col-md-4"><div class="text-muted small">Percentage change</div><strong>{{ number_format((float) $record->difference_percent, 2) }}%</strong></div>
                    </div>
                @else
                    <p class="text-muted mb-0">A comparable verified rate for the previous financial year was not available.</p>
                @endif
            </div>
            @endif
            <div class="alert alert-warning">{{ $disclaimer }}</div>
            <div class="d-flex flex-wrap gap-2 justify-content-center">
                <a class="th-btn" href="{{ route('circle-rates.calculator') }}">Recalculate</a>
                <button class="th-btn style2" type="button" onclick="window.print()">Print / Save PDF</button>
                @if($allowSharing)<button class="th-btn style2" type="button" onclick="navigator.clipboard.writeText(window.location.href).then(() => alert('Result link copied.'))">Copy share link</button>@endif
            </div>
        </div></div>
    </div>
</section>
@endsection
