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
            <a href="{{ route('admin.homepages.preview', $homepage) }}" class="btn btn-outline-primary" target="_blank"
                rel="noopener">Preview draft</a>
            <form method="POST" action="{{ route('admin.homepages.verify', $homepage) }}"
                onsubmit="return confirm('Have you compared every section with the original Piller HTML and checked desktop, tablet and mobile layouts, images, links and interactive behavior? The server will also verify that every required section is registered.')">
                @csrf
                @method('PATCH')
                <button class="btn btn-outline-success" type="submit">Verify composition</button>
            </form>
            <form method="POST" action="{{ route('admin.homepages.publish', $homepage) }}"
                onsubmit="return confirm('Publish this draft? It will not become the live homepage until you select it as the default.')">
                @csrf
                @method('PATCH')
                <button class="btn btn-success" type="submit">Publish draft</button>
            </form>
        </div>
    </div>
    @if (!$homepage->is_composition_complete)
        <div class="alert alert-warning" role="status">
            This draft is an incomplete section composition. Its preview is for the sections currently added to the canvas
            only. The live site continues to use the original complete homepage until every source block has been converted
            and verified.
        </div>
    @endif

    <div class="row g-4">
        <div class="col-xl-4">
            <div class="content-card">
                <h2 class="section-title">Section library</h2>
                <p class="text-muted small">The catalog includes the sections extracted so far from the original designs. A
                    section being available here does not mean it has passed the strict visual, interaction and responsive
                    completion checks.</p>
                @php($sectionGroups = collect($availableSections)->groupBy('type'))
                @foreach ($sectionGroups as $groupName => $groupSections)
                    <details class="border rounded mb-2" @if ($loop->first) open @endif>
                        <summary class="p-2 fw-semibold d-flex justify-content-between align-items-center">
                            <span>{{ \Illuminate\Support\Str::headline($groupName) }}</span>
                            <span class="badge bg-light text-dark">{{ $groupSections->count() }} designs</span>
                        </summary>
                        <div class="p-2">
                            @foreach ($groupSections as $section)
                                <form method="POST" action="{{ route('admin.homepages.sections.store', $homepage) }}"
                                    class="d-flex flex-wrap justify-content-between align-items-center gap-2 border rounded p-2 mb-2">
                                    @csrf
                                    <input type="hidden" name="section_key" value="{{ $section['key'] }}">
                                    <div>
                                        <div class="fw-semibold">{{ $section['label'] }}</div>
                                        <div class="text-muted small">Design: {{ $section['source_design'] }}</div>
                                    </div>
                                    <div class="d-flex gap-1">
                                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.sections.preview', $section['key']) }}" target="_blank" rel="noopener">Preview section</a>
                                        <button class="btn btn-sm btn-outline-primary" type="submit">Add</button>
                                    </div>
                                </form>
                            @endforeach
                        </div>
                    </details>
                @endforeach
                <hr class="my-4">
                <h3 class="h6 fw-semibold">Saved reusable sections</h3>
                @forelse($reusableSections as $reusableSection)
                    <form method="POST"
                        action="{{ route('admin.homepages.reusable-sections.attach', [$homepage, $reusableSection]) }}"
                        class="d-flex justify-content-between align-items-center gap-2 border rounded p-2 mb-2">
                        @csrf
                        <div>
                            <div class="fw-semibold">{{ $reusableSection->name }}</div>
                            <div class="text-muted small">{{ $reusableSection->section_key }} · From
                                {{ $reusableSection->source_design }}</div>
                        </div>
                        <button class="btn btn-sm btn-outline-success" type="submit">Insert</button>
                    </form>
                @empty
                    <p class="small text-muted mb-0">No reusable sections have been saved yet. Save one from the homepage
                        canvas.</p>
                @endforelse
            </div>
        </div>

        <div class="col-xl-8">
            <div class="content-card">
                <div class="d-flex justify-content-between align-items-center gap-2 mb-3">
                    <h2 class="section-title mb-0">Homepage canvas</h2>
                    <span class="small text-muted" id="section-save-status" role="status">Order is saved automatically
                        after dragging.</span>
                </div>

                <div id="homepage-section-list"
                    data-reorder-url="{{ route('admin.homepages.sections.reorder', $homepage) }}">
                    @forelse($homepage->sections as $section)

                        @php
                            $definition = $availableSections[$section->section_key] ?? null;
                            $querySchema = $queryableTypes[$section->section_type] ?? null;
                            $dataSource = $section->data_source ?? [];
                        @endphp
                        <div class="border rounded p-3 mb-2 d-flex flex-wrap justify-content-between align-items-center gap-3 homepage-section-item {{ $section->is_enabled ? '' : 'opacity-50' }}"
                            draggable="true" data-section-id="{{ $section->id }}">
                            <div class="d-flex align-items-center gap-3">
                                <span class="text-muted fs-4" aria-hidden="true">⠿</span>
                                <div>
                                    <div class="fw-semibold">{{ $definition['label'] ?? $section->section_key }}</div>
                                    <div class="small text-muted">{{ $section->section_type }} · Source:
                                        {{ $section->source_design }} · #{{ $section->sort_order }}</div>
                                </div>
                            </div>
                            @if (!empty($definition['fields']))
                                <form method="POST"
                                    action="{{ route('admin.homepages.sections.update', [$homepage, $section]) }}"
                                    class="w-100 row g-2 align-items-end">
                                    @csrf
                                    @method('PATCH')
                                    @foreach ($definition['fields'] as $field => $schema)
                                        @php
                                            $fieldValue = old(
                                                'config.' . $field,
                                                $section->config[$field] ??
                                                    ($section->reusableSection?->config[$field] ?? ''),
                                            );
                                            $fieldMedia =
                                                $section->mediaAttachments->firstWhere('field_key', $field)?->media ??
                                                $section->reusableSection?->mediaAttachments?->firstWhere(
                                                    'field_key',
                                                    $field,
                                                )?->media;
                                            $fieldPreviewUrl =
                                                $fieldMedia?->url ??
                                                (is_string($fieldValue) && !ctype_digit($fieldValue)
                                                    ? $fieldValue
                                                    : '');
                                        @endphp
                                        <div
                                            class="{{ in_array($schema['type'], ['textarea', 'richtext'], true) ? 'col-12' : 'col-md-6' }}">
                                            <label class="form-label small"
                                                for="section-{{ $section->id }}-{{ $field }}">{{ $schema['label'] }}</label>
                                            @if (in_array($schema['type'], ['textarea', 'richtext'], true))
                                                <textarea class="form-control form-control-sm" id="section-{{ $section->id }}-{{ $field }}"
                                                    name="config[{{ $field }}]" rows="3" maxlength="{{ $schema['max'] ?? 1500 }}" @if ($schema['type'] === 'richtext') data-rich-editor @endif>{{ $fieldValue }}</textarea>
                                            @elseif($schema['type'] === 'select')
                                                <select class="form-select form-select-sm"
                                                    id="section-{{ $section->id }}-{{ $field }}"
                                                    name="config[{{ $field }}]">
                                                    <option value="">Use template default</option>
                                                    @foreach ($schema['options'] as $optionValue => $optionLabel)
                                                        <option value="{{ $optionValue }}" @selected((string) $fieldValue === (string) $optionValue)>
                                                            {{ $optionLabel }}</option>
                                                    @endforeach
                                                </select>
                                            @elseif($schema['type'] === 'image')
                                                <input type="hidden" id="section-{{ $section->id }}-{{ $field }}"
                                                    name="config[{{ $field }}]" value="{{ $fieldValue }}"
                                                    data-media-value>
                                                <div class="d-flex align-items-center gap-2">
                                                    <img src="{{ $fieldPreviewUrl }}"
                                                        alt="{{ $fieldMedia?->alt_text ?: 'Selected section media' }}"
                                                        class="rounded border"
                                                        style="width:76px;height:56px;object-fit:cover" data-media-preview
                                                        @if (!$fieldPreviewUrl) hidden @endif>
                                                    <span class="small text-muted"
                                                        data-media-meta>{{ $fieldMedia ? $fieldMedia->original_name . ' · ' . number_format($fieldMedia->size / 1024, 1) . ' KB' : '' }}</span>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                                        data-open-media-picker
                                                        data-target="section-{{ $section->id }}-{{ $field }}">Choose
                                                        from Media Library</button>
                                                    <button type="button" class="btn btn-sm btn-link text-danger"
                                                        data-clear-media
                                                        data-target="section-{{ $section->id }}-{{ $field }}">Clear</button>
                                                </div>
                                            @elseif($schema['type'] === 'checkbox')
                                                <input type="hidden" name="config[{{ $field }}]" value="0">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="section-{{ $section->id }}-{{ $field }}"
                                                        name="config[{{ $field }}]" value="1"
                                                        @checked((bool) $fieldValue)>
                                                    <label class="form-check-label small"
                                                        for="section-{{ $section->id }}-{{ $field }}">Enabled</label>
                                                </div>
                                            @else
                                                <input class="form-control form-control-sm"
                                                    id="section-{{ $section->id }}-{{ $field }}"
                                                    name="config[{{ $field }}]"
                                                    type="{{ $schema['type'] === 'number' ? 'number' : 'text' }}"
                                                    value="{{ $fieldValue }}"
                                                    @if (isset($schema['max']) && $schema['type'] !== 'number') maxlength="{{ $schema['max'] }}" @endif
                                                    @if ($schema['type'] === 'number') min="{{ $schema['min'] ?? 0 }}" max="{{ $schema['max'] ?? 9999 }}" @endif
                                                    @if ($schema['type'] === 'color') pattern="#[0-9A-Fa-f]{6}" @endif
                                                    @if ($schema['type'] === 'url') placeholder="/properties or https://example.com" @endif>
                                            @endif
                                        </div>
                                    @endforeach
                                    @if ($querySchema)
                                        <div class="col-12"><hr><h6 class="mb-2">Database query</h6></div>
                                        <div class="col-md-3">
                                            <label class="form-label small">Items limit</label>
                                            <input class="form-control form-control-sm" type="number" min="1" max="24" name="data_source[limit]" value="{{ $dataSource['limit'] ?? ($section->config['items_limit'] ?? 6) }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small">Sort by</label>
                                            <select class="form-select form-select-sm" name="data_source[sort_by]">
                                                @foreach ($querySchema['sort'] as $sort)
                                                    <option value="{{ $sort }}" @selected(($dataSource['sort_by'] ?? 'published_at') === $sort)>{{ ucfirst(str_replace('_', ' ', $sort)) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small">Direction</label>
                                            <select class="form-select form-select-sm" name="data_source[sort_direction]">
                                                <option value="desc" @selected(($dataSource['sort_direction'] ?? 'desc') === 'desc')>DESC</option>
                                                <option value="asc" @selected(($dataSource['sort_direction'] ?? '') === 'asc')>ASC</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small">Columns</label>
                                            <input class="form-control form-control-sm" type="number" min="1" max="6" name="data_source[columns]" value="{{ $dataSource['columns'] ?? ($section->config['columns'] ?? 3) }}">
                                        </div>
                                        @foreach ($querySchema['filters'] ?? [] as $filter => $filterSchema)
                                            <div class="col-md-4">
                                                <label class="form-label small">{{ $filterSchema['label'] }}</label>
                                                @if (($filterSchema['type'] ?? '') === 'select')
                                                    <select class="form-select form-select-sm" name="data_source[filters][{{ $filter }}]">
                                                        @foreach ($filterSchema['options'] as $option => $label)
                                                            <option value="{{ $option }}" @selected(($dataSource['filters'][$filter] ?? '') === (string) $option)>{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <input class="form-control form-control-sm" type="{{ in_array($filterSchema['type'] ?? '', ['integer', 'number'], true) ? 'number' : 'text' }}" name="data_source[filters][{{ $filter }}]" value="{{ $dataSource['filters'][$filter] ?? '' }}" @if(isset($filterSchema['min'])) min="{{ $filterSchema['min'] }}" @endif @if(isset($filterSchema['max'])) max="{{ $filterSchema['max'] }}" @endif @if(isset($filterSchema['max']) && ($filterSchema['type'] ?? '') === 'text') maxlength="{{ $filterSchema['max'] }}" @endif>
                                                @endif
                                            </div>
                                        @endforeach
                                        <div class="col-md-6">
                                            <label class="form-label small">Only these record IDs (comma separated, optional)</label>
                                            <input class="form-control form-control-sm" name="data_source[selected_ids]" value="{{ implode(',', $dataSource['selected_ids'] ?? []) }}" placeholder="e.g. 2,5,8">
                                        </div>
                                        @if ($querySchema['slider'] ?? true)
                                            <div class="col-md-3 pt-4">
                                                <input type="hidden" name="data_source[slider]" value="0">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="data_source[slider]" value="1" @checked($dataSource['slider'] ?? true)>
                                                    <label class="form-check-label small">Enable slider</label>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                    <div class="col-12">
                                        <button class="btn btn-sm btn-outline-primary" type="submit">Save section
                                            settings</button>
                                    </div>
                                </form>
                            @endif
                            <div class="d-flex flex-wrap gap-2">
                                <form method="POST"
                                    action="{{ route('admin.homepages.sections.save-reusable', [$homepage, $section]) }}"
                                    class="d-flex gap-1 align-items-center">
                                    @csrf
                                    <input class="form-control form-control-sm" type="text" name="name"
                                        maxlength="120" placeholder="Reusable section name" required
                                        aria-label="Reusable section name">
                                    <button class="btn btn-sm btn-outline-success" type="submit">Save to library</button>
                                </form>
                                <form method="POST"
                                    action="{{ route('admin.homepages.sections.duplicate', [$homepage, $section]) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-primary" type="submit">Duplicate</button>
                                </form>
                                <form method="POST"
                                    action="{{ route('admin.homepages.sections.toggle', [$homepage, $section]) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-sm btn-outline-secondary"
                                        type="submit">{{ $section->is_enabled ? 'Disable' : 'Enable' }}</button>
                                </form>
                                <form method="POST"
                                    action="{{ route('admin.homepages.sections.destroy', [$homepage, $section]) }}"
                                    onsubmit="return confirm('Remove this section from the draft?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Remove</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-5 border rounded">Your draft is empty. Add sections from the
                            library to start composing it.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>


@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.tinymce) {
                tinymce.init({
                    selector: 'textarea[data-rich-editor]',
                    license_key: 'gpl',
                    menubar: false,
                    plugins: 'lists link',
                    toolbar: 'undo redo | blocks | bold italic underline | bullist numlist | link | removeformat',
                    height: 240,
                    branding: false,
                    promotion: false,
                    setup: editor => editor.on('change keyup undo redo', () => editor.save())
                });
                document.querySelectorAll('form').forEach(form => {
                    form.addEventListener('submit', () => tinymce.triggerSave());
                });
            }
            const list = document.getElementById('homepage-section-list');
            const status = document.getElementById('section-save-status');
            if (!list) return;

            let dragged = null;

            list.addEventListener('dragstart', function(event) {
                const item = event.target.closest('.homepage-section-item');
                if (!item) return;
                dragged = item;
                event.dataTransfer.effectAllowed = 'move';
                item.classList.add('opacity-50');
            });

            list.addEventListener('dragover', function(event) {
                event.preventDefault();
                const target = event.target.closest('.homepage-section-item');
                if (!dragged || !target || target === dragged) return;

                const bounds = target.getBoundingClientRect();
                const insertAfter = event.clientY > bounds.top + bounds.height / 2;
                list.insertBefore(dragged, insertAfter ? target.nextSibling : target);
            });

            list.addEventListener('drop', async function(event) {
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
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .content,
                        },
                        body: JSON.stringify({
                            sections: ids
                        }),
                    });

                    if (!response.ok) throw new Error('The new order could not be saved.');
                    status.textContent = 'Order saved.';
                } catch (error) {
                    status.textContent = error.message;
                    window.location.reload();
                }
            });

            list.addEventListener('dragend', function() {
                if (dragged) dragged.classList.remove('opacity-50');
                dragged = null;
            });


        });
    </script>
@endpush


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.homepage-section-item').forEach((item, index) => {
        const header = item.firstElementChild;
        if (!header) return;
        const sectionId = item.dataset.sectionId || String(index);
        const content = Array.from(item.children).filter(child => child !== header);
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'btn btn-sm btn-outline-secondary ms-auto';
        button.textContent = 'Collapse';
        button.setAttribute('aria-expanded', 'true');
        button.addEventListener('click', () => {
            const collapsed = button.getAttribute('aria-expanded') === 'true';
            content.forEach(child => child.hidden = collapsed);
            button.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
            button.textContent = collapsed ? 'Expand section' : 'Collapse';
            try { localStorage.setItem('homepage-section-collapsed:' + sectionId, collapsed ? '1' : '0'); } catch (_) {}
        });
        header.append(button);
        let collapsed = false;
        try { collapsed = localStorage.getItem('homepage-section-collapsed:' + sectionId) === '1'; } catch (_) {}
        if (collapsed) {
            content.forEach(child => child.hidden = true);
            button.setAttribute('aria-expanded', 'false');
            button.textContent = 'Expand section';
        }
    });
});
</script>
@endpush
