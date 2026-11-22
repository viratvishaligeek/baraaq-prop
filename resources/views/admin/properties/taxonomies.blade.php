@extends('admin.layouts.app')
@section('title', $taxonomy === 'types' ? 'Property types' : 'Property amenities')
@section('content')
    <div class="mb-4"><h1 class="page-title mb-1">{{ $taxonomy === 'types' ? 'Property types' : 'Property amenities' }}</h1><p class="text-muted mb-0">{{ $taxonomy === 'types' ? 'Distinct types currently used by properties.' : 'Distinct amenities and features currently assigned to properties.' }} Add or edit values directly in the property editor.</p></div>
    <div class="content-card">
        <div class="table-responsive"><table class="table align-middle" data-datatable><thead><tr><th>{{ $taxonomy === 'types' ? 'Property type' : 'Amenity / feature' }}</th><th>Properties</th><th></th></tr></thead><tbody>
            @forelse($items as $item)
                @php($name = $taxonomy === 'types' ? $item->property_type : $item->name)
                <tr><td class="fw-semibold">{{ $name }}</td><td>{{ $item->properties_count }}</td><td class="text-end"><a class="btn btn-sm btn-light" href="{{ route('admin.properties.index', $taxonomy === 'types' ? ['property_type' => $name] : ['feature' => $name]) }}">View properties</a></td></tr>
            @empty
                <tr><td colspan="3" class="text-center text-muted py-5">No {{ $taxonomy }} recorded yet.</td></tr>
            @endforelse
        </tbody></table></div>
    </div>
@endsection
