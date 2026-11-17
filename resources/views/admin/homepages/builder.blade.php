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
@if(!$homepage->is_composition_complete)
    <div class="alert alert-warning" role="status">
        This draft is an incomplete section composition. Its preview is for the sections currently added to the canvas only. The live site continues to use the original complete homepage until every source block has been converted and verified.
    </div>
@endif

<div class="row g-4">
    <div class="col-xl-4">
        <div class="content-card">
            <h2 class="section-title">Section library</h2>
            <p class="text-muted small">Available now: ten hero variants and the first property, team and blog sections. This is not yet the complete source library; the remaining original blocks are being converted design by design.</p>
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
            <hr class="my-4">
            <h3 class="h6 fw-semibold">Saved reusable sections</h3>
            @forelse($reusableSections as $reusableSection)
                <form method="POST" action="{{ route('admin.homepages.reusable-sections.attach', [$homepage, $reusableSection]) }}" class="d-flex justify-content-between align-items-center gap-2 border rounded p-2 mb-2">
                    @csrf
                    <div>
                        <div class="fw-semibold">{{ $reusableSection->name }}</div>
                        <div class="text-muted small">{{ $reusableSection->section_key }} · From {{ $reusableSection->source_design }}</div>
                    </div>
                    <button class="btn btn-sm btn-outline-success" type="submit">Insert</button>
                </form>
            @empty
                <p class="small text-muted mb-0">No reusable sections have been saved yet. Save one from the homepage canvas.</p>
            @endforelse
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
                                    @php
                                        $fieldValue = old('config.'.$field, $section->config[$field] ?? $section->reusableSection?->config[$field] ?? '');
                                        $fieldMedia = $section->mediaAttachments->firstWhere('field_key', $field)?->media
                                            ?? $section->reusableSection?->mediaAttachments?->firstWhere('field_key', $field)?->media;
                                        $fieldPreviewUrl = $fieldMedia?->url ?? (is_string($fieldValue) && !ctype_digit($fieldValue) ? $fieldValue : '');
                                    @endphp
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
                                        @elseif($schema['type'] === 'image')
                                            <input type="hidden" id="section-{{ $section->id }}-{{ $field }}" name="config[{{ $field }}]" value="{{ $fieldValue }}" data-media-value>
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="{{ $fieldPreviewUrl }}" alt="{{ $fieldMedia?->alt_text ?: 'Selected section media' }}" class="rounded border" style="width:76px;height:56px;object-fit:cover" data-media-preview @if(!$fieldPreviewUrl) hidden @endif>
                                                <span class="small text-muted" data-media-meta>{{ $fieldMedia ? $fieldMedia->original_name.' · '.number_format($fieldMedia->size / 1024, 1).' KB' : '' }}</span>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" data-open-media-picker data-target="section-{{ $section->id }}-{{ $field }}">Choose from Media Library</button>
                                                <button type="button" class="btn btn-sm btn-link text-danger" data-clear-media data-target="section-{{ $section->id }}-{{ $field }}">Clear</button>
                                            </div>
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
                                                type="{{ $schema['type'] === 'number' ? 'number' : 'text' }}"
                                                value="{{ $fieldValue }}"
                                                @if(isset($schema['max']) && $schema['type'] !== 'number') maxlength="{{ $schema['max'] }}" @endif
                                                @if($schema['type'] === 'number') min="{{ $schema['min'] ?? 0 }}" max="{{ $schema['max'] ?? 9999 }}" @endif
                                                @if($schema['type'] === 'color') pattern="#[0-9A-Fa-f]{6}" @endif
                                                @if($schema['type'] === 'url') placeholder="/properties or https://example.com" @endif
                                            >
                                        @endif
                                    </div>
                                @endforeach
                                <div class="col-12"><button class="btn btn-sm btn-outline-primary" type="submit">Save section settings</button></div>
                            </form>
                        @endif
                        <div class="d-flex flex-wrap gap-2">
                            <form method="POST" action="{{ route('admin.homepages.sections.save-reusable', [$homepage, $section]) }}" class="d-flex gap-1 align-items-center">
                                @csrf
                                <input class="form-control form-control-sm" type="text" name="name" maxlength="120" placeholder="Reusable section name" required aria-label="Reusable section name">
                                <button class="btn btn-sm btn-outline-success" type="submit">Save to library</button>
                            </form>
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

<div class="modal fade" id="homepage-media-picker" tabindex="-1" aria-labelledby="homepage-media-picker-title" aria-hidden="true" data-endpoint="{{ route('admin.media.picker') }}">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="homepage-media-picker-title">Choose image from Media Library</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="d-flex gap-2 mb-3" id="homepage-media-search">
                    <input type="search" class="form-control" id="homepage-media-query" placeholder="Search image filename">
                    <button class="btn btn-outline-primary" type="submit">Search</button>
                </form>
                <div class="row g-3" id="homepage-media-results" aria-live="polite"></div>
                <div class="text-center mt-3"><button type="button" class="btn btn-outline-secondary" id="homepage-media-more" hidden>Load more</button></div>
                <p class="small text-muted mt-2 mb-0" id="homepage-media-status">Loading images…</p>
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

    const picker = document.getElementById('homepage-media-picker');
    if (picker && window.bootstrap?.Modal) {
        const modal = bootstrap.Modal.getOrCreateInstance(picker);
        const results = document.getElementById('homepage-media-results');
        const status = document.getElementById('homepage-media-status');
        const more = document.getElementById('homepage-media-more');
        const query = document.getElementById('homepage-media-query');
        let targetInput = null;
        let nextPage = null;

        async function loadMedia(url, append = false) {
            status.textContent = 'Loading images…';
            more.hidden = true;
            try {
                const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
                if (!response.ok) throw new Error('Media Library could not be loaded.');
                const payload = await response.json();
                if (!append) results.replaceChildren();
                payload.data.forEach((item) => {
                    const col = document.createElement('div');
                    col.className = 'col-6 col-sm-4 col-md-3 col-xl-2';
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'btn btn-light border w-100 h-100 p-2 text-start';
                    const image = document.createElement('img');
                    image.src = item.url;
                    image.alt = item.alt || item.name;
                    image.className = 'w-100 rounded mb-2';
                    image.style.cssText = 'height:110px;object-fit:cover';
                    const name = document.createElement('span');
                    name.className = 'small d-block text-break';
                    name.textContent = item.name;
                    button.append(image, name);
                    button.addEventListener('click', () => {
                        if (!targetInput) return;
                        targetInput.value = String(item.id);
                        const preview = targetInput.parentElement.querySelector('[data-media-preview]');
                        if (preview) { preview.src = item.url; preview.alt = item.alt || item.name; preview.hidden = false; }\n                        const meta = targetInput.parentElement.querySelector('[data-media-meta]');\n                        if (meta) meta.textContent = item.name + ' · ' + (Number(item.size || 0) / 1024).toFixed(1) + ' KB';
                        modal.hide();
                    });
                    col.append(button);
                    results.append(col);
                });
                nextPage = payload.next_page_url;
                more.hidden = !nextPage;
                status.textContent = payload.data.length ? 'Select an image to use it in this section.' : 'No images found. Upload images in Media Library first.';
            } catch (error) {
                status.textContent = error.message;
            }
        }

        document.querySelectorAll('[data-open-media-picker]').forEach((button) => {
            button.addEventListener('click', () => {
                targetInput = document.getElementById(button.dataset.target);
                query.value = '';
                modal.show();
                loadMedia(picker.dataset.endpoint);
            });
        });
        document.querySelectorAll('[data-clear-media]').forEach((button) => {
            button.addEventListener('click', () => {
                const input = document.getElementById(button.dataset.target);
                if (!input) return;
                input.value = '';
                const preview = input.parentElement.querySelector('[data-media-preview]');
                if (preview) preview.hidden = true;\n                const meta = input.parentElement.querySelector('[data-media-meta]');\n                if (meta) meta.textContent = '';
            });
        });
        document.getElementById('homepage-media-search').addEventListener('submit', (event) => {
            event.preventDefault();
            const url = new URL(picker.dataset.endpoint, window.location.origin);
            if (query.value.trim()) url.searchParams.set('search', query.value.trim());
            loadMedia(url.toString());
        });
        more.addEventListener('click', () => { if (nextPage) loadMedia(nextPage, true); });
    }
});
</script>
@endpush
