@extends('admin.layouts.app')
@section('title', 'Dashboard widget management')
@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div><h1 class="page-title mb-1">Dashboard widgets</h1><p class="text-muted mb-0">Choose the KPIs shown on your dashboard and configure their destinations and order.</p></div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-light">Back to dashboard</a>
    </div>
    <form method="POST" action="{{ route('admin.dashboard.widgets.update') }}">
        @csrf @method('PUT')
        <div class="content-card">
            @foreach ($widgets as $key => $widget)
                @php($saved = $configuration[$key] ?? [])
                <div class="row align-items-center g-3 py-3 border-bottom">
                    <div class="col-md-4"><div class="d-flex align-items-center gap-2"><i class="fa {{ $widget['icon'] }} text-muted"></i><strong>{{ $widget['label'] }}</strong></div><div class="small text-muted">Available KPI</div></div>
                    <div class="col-6 col-md-2">
                        <input type="hidden" name="widgets[{{ $key }}][enabled]" value="0">
                        <label class="form-check"><input class="form-check-input" type="checkbox" name="widgets[{{ $key }}][enabled]" value="1" @checked($saved['enabled'] ?? true)> <span class="form-check-label">Enabled</span></label>
                    </div>
                    <div class="col-8 col-md-4"><label class="form-label small" for="destination-{{ $key }}">Click destination</label><select class="form-select form-select-sm" id="destination-{{ $key }}" name="widgets[{{ $key }}][destination]">@foreach ($widget['destinations'] as $destinationKey => $destination)<option value="{{ $destinationKey }}" @selected(($saved['destination'] ?? $widget['default']) === $destinationKey)>{{ $destination['label'] }}</option>@endforeach</select></div>
                    <div class="col-4 col-md-2"><label class="form-label small" for="order-{{ $key }}">Order</label><input class="form-control form-control-sm" type="number" min="0" max="1000" id="order-{{ $key }}" name="widgets[{{ $key }}][order]" value="{{ $saved['order'] ?? $loop->iteration }}"></div>
                </div>
            @endforeach
            <div class="d-flex justify-content-end pt-3"><button class="btn btn-primary">Save dashboard layout</button></div>
        </div>
    </form>
@endsection
