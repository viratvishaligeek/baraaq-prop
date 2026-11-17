@extends('admin.layouts.app')

@section('title', 'Homepage builder')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="page-title mb-1">Homepage builder</h1>
        <p class="text-muted mb-0">{{ $homepage->name }} · Draft · Drag sections to change their order.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.homepages.index') }}" class="btn btn-light">Back</a>
        <a href="{{ route('admin.homepages.preview', $homepage) }}" class="btn btn-outline-primary" target="_blank" rel="noopener">Preview draft</a>
        <form method="POST" action="{{ route('admin.homepages.publish', $homepage) }}" onsubmit="return confirm('Publish this draft? It will not become the live homepage until you select it as the default.')">
            @csrf
            @method('PATCH')
            <button class="btn btn-success" type="submit">Publish draft</button>
        </form>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-4">
        <div class="content-card">
            <h2 class="section-title">Section library</h2>
            <p class="text-muted small">The initial library includes all ten source hero designs and dynamic property, team and blog sections. More source sections are being added in the theme conversion phase.</p>
            @foreach($availableSections as $section)
                <form method="POST" action="{{ route('admin.homepages.sections.store', $homepage) }}" class="d-flex justify-content-between align-items-center gap-2 border rounded p-2 mb-2">
                    @csrf
                    <input type="hidden" name="section_key" value="{{ $section['key'] }}">
                    <div>
                        <div class="fw-semibold">{{ $section['label'] }}</div>
                        <div class="text-muted small">From {{ $section['source_design'] }}</div>
                    </div>
                    <button class="btn btn-sm btn-outline-primary" type="submit">Add</button>
                </form>
            @endforeach
        </div>
    </div>

    <div class="col-xl-8">
        <div class="content-card">
            <div class="d-flex justify-content-between align-items-center gap-2 mb-3">
                <h2 class="section-title mb-0">Homepage canvas</h2>
                <span class="small text-muted" id="section-save-status" role="status">Order is saved automatically after dragging.</span>
            </div>

            <div id="homepage-section-list" data-reorder-url="{{ route('admin.homepages.sections.reorder', $homepage) }}">
                @forelse($homepage->sections as $section)
                    @php($definition = $availableSections[$section->section_key] ?? null)
                    <div class="border rounded p-3 mb-2 d-flex flex-wrap justify-content-between align-items-center gap-3 homepage-section-item {{ $section->is_enabled ? '' : 'opacity-50' }}" draggable="true" data-section-id="{{ $section->id }}">
                        <div class="d-flex align-items-center gap-3">
                            <span class="text-muted fs-4" aria-hidden="true">⠿</span>
                            <div>
                                <div class="fw-semibold">{{ $definition['label'] ?? $section->section_key }}</div>
                                <div class="small text-muted">{{ $section->section_type }} · Source: {{ $section->source_design }} · #{{ $section->sort_order }}</div>
                            </div>
                        </div>
                        @if(!empty($definition['fields']))
                            <form method="POST" action="{{ route('admin.homepages.sections.update', [$homepage, $section]) }}" class="w-100 row g-2 align-items-end">
                                @csrf
                                @method('PATCH')
                                @foreach($definition['fields'] as $field => $schema)
                                    @php($fieldValue = old('config.'.$field, $section->config[$field] ?? ''))
                                    <div class="{{ in_array($schema['type'], ['textarea'], true) ? 'col-12' : 'col-md-6' }}">
                                        <label class="form-label small" for="section-{{ $section->id }}-{{ $field }}">{{ $schema['label'] }}</label>
                                        @if($schema['type'] === 'textarea')
                                            <textarea class="form-control form-control-sm" id="section-{{ $section->id }}-{{ $field }}" name="config[{{ $field }}]" rows="2" maxlength="{{ $schema['max'] ?? 1500 }}">{{ $fieldValue }}</textarea>
                                        @elseif($schema['type'] === 'select')
                                            <select class="form-select form-select-sm" id="section-{{ $section->id }}-{{ $field }}" name="config[{{ $field }}]">
                                                <option value="">Use template default</option>
                                                @foreach($schema['options'] as $optionValue => $optionLabel)
                                                    <option value="{{ $optionValue }}" @selected((string) $fieldValue === (string) $optionValue)>{{ $optionLabel }}</option>
                                                @endforeach
                                            </select>
                                        @elseif($schema['type'] === 'checkbox')
                                            <input type="hidden" name="config[{{ $field }}]" value="0">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="section-{{ $section->id }}-{{ $field }}" name="config[{{ $field }}]" value="1" @checked((bool) $fieldValue)>
                                                <label class="form-check-label small" for="section-{{ $section->id }}-{{ $field }}">Enabled</label>
                                            </div>
                                        @else
                                            <input
                                                class="form-control form-control-sm"
                                                id="section-{{ $section->id }}-{{ $field }}"
                                                name="config[{{ $field }}]"
                                                type="{{ $schema['type'] === 'number' ? 'number' : ($schema['type'] === 'color' ? 'color' : 'text') }}"
                                                value="{{ $fieldValue }}"
                                                @if(isset($schema['max']) && $schema['type'] !== 'number') maxlength="{{ $schema['max'] }}" @endif
                                                @if($schema['type'] === 'number') min="{{ $schema['min'] ?? 0 }}" max="{{ $schema['max'] ?? 9999 }}" @endif
                                                @if($schema['type'] === 'color') pattern="#[0-9A-Fa-f]{6}" @endif
                                                @if(in_array($schema['type'], ['url', 'image'], true)) placeholder="{{ $schema['type'] === 'url' ? '/properties or https://example.com' : 'theme-assets/img/... or media path' }}" @endif
                                            >
                                        @endif
                                    </div>
                                @endforeach
                                <div class="col-12"><button class="btn btn-sm btn-outline-primary" type="submit">Save section settings</button></div>
                            </form>
                        @endif
                        <div class="d-flex gap-2">
                            <form method="POST" action="{{ route('admin.homepages.sections.duplicate', [$homepage, $section]) }}">
                                @csrf
                                <button class="btn btn-sm btn-outline-primary" type="submit">Duplicate</button>
                            </form>
                            <form method="POST" action="{{ route('admin.homepages.sections.toggle', [$homepage, $section]) }}">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-sm btn-outline-secondary" type="submit">{{ $section->is_enabled ? 'Disable' : 'Enable' }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.homepages.sections.destroy', [$homepage, $section]) }}" onsubmit="return confirm('Remove this section from the draft?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Remove</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-5 border rounded">Your draft is empty. Add sections from the library to start composing it.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const list = document.getElementById('homepage-section-list');
    const status = document.getElementById('section-save-status');
    if (!list) return;

    let dragged = null;

    list.addEventListener('dragstart', function (event) {
        const item = event.target.closest('.homepage-section-item');
        if (!item) return;
        dragged = item;
        event.dataTransfer.effectAllowed = 'move';
        item.classList.add('opacity-50');
    });

    list.addEventListener('dragover', function (event) {
        event.preventDefault();
        const target = event.target.closest('.homepage-section-item');
        if (!dragged || !target || target === dragged) return;

        const bounds = target.getBoundingClientRect();
        const insertAfter = event.clientY > bounds.top + bounds.height / 2;
        list.insertBefore(dragged, insertAfter ? target.nextSibling : target);
    });

    list.addEventListener('drop', async function (event) {
        event.preventDefault();
        if (!dragged) return;

        dragged.classList.remove('opacity-50');
        dragged = null;
        const ids = Array.from(list.querySelectorAll('[data-section-id]'))
            .map((item) => Number(item.dataset.sectionId));

        status.textContent = 'Saving order…';

        try {
            const response = await fetch(list.dataset.reorderUrl, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ sections: ids }),
            });

            if (!response.ok) throw new Error('The new order could not be saved.');
            status.textContent = 'Order saved.';
        } catch (error) {
            status.textContent = error.message;
            window.location.reload();
        }
    });

    list.addEventListener('dragend', function () {
        if (dragged) dragged.classList.remove('opacity-50');
        dragged = null;
    });
});
</script>
@endpush
