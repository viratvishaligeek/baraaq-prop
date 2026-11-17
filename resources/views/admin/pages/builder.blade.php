@extends('admin.layouts.app')

@section('title', 'Page builder')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="page-title mb-1">Page builder</h1>
            <p class="text-muted mb-0">{{ $page->title }} · {{ ucfirst($page->status) }} · Drag widgets to reorder them.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.content.index', 'pages') }}" class="btn btn-light">Back to pages</a>
            <a href="{{ route('admin.pages.preview', $page) }}" class="btn btn-outline-primary" target="_blank" rel="noopener">Preview</a>
            @if ($page->status === 'published')
                <form method="POST" action="{{ route('admin.pages.duplicate', $page) }}">
                    @csrf
                    <button class="btn btn-outline-secondary">Create draft copy</button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.pages.publish', $page) }}" onsubmit="return confirm('Publish this page now?')">
                    @csrf
                    @method('PATCH')
                    <button class="btn btn-success">Publish page</button>
                </form>
            @endif
        </div>
    </div>

    @if ($page->status === 'published')
        <div class="alert alert-info">This page is live. To avoid changing the published page, create a draft copy before making edits.</div>
    @endif

    <div class="row g-4">
        <div class="col-xl-3">
            <div class="content-card">
                <h2 class="section-title">Widget library</h2>
                <p class="small text-muted">Choose an existing Piller section design. No replacement layouts are generated.</p>
                <input type="search" class="form-control form-control-sm mb-3" id="widget-library-search" placeholder="Search sections and designs…" aria-label="Search widget library">
                @foreach ($widgetGroups as $group => $widgets)
                    <h3 class="h6 mt-3">{{ $group }}</h3>
                    @foreach ($widgets as $widget)
                        <form method="POST" action="{{ route('admin.pages.widgets.add', $page) }}" class="border rounded p-2 mb-2 d-flex align-items-center justify-content-between gap-2 widget-library-item" data-search="{{ strtolower($widget['label'].' '.$widget['design'].' '.$widget['type'].' '.$widget['key']) }}">
                            @csrf
                            <input type="hidden" name="section_key" value="{{ $widget['key'] }}">
                            <div>
                                <div class="fw-semibold small">{{ $widget['label'] }}</div>
                                <div class="text-muted" style="font-size:.75rem">{{ $widget['design'] }} · {{ $widget['type'] }}</div>
                            </div>
                            <button class="btn btn-sm btn-outline-primary" {{ $page->status !== 'draft' ? 'disabled' : '' }}>Add</button>
                        </form>
                    @endforeach
                @endforeach
            </div>
        </div>

        <div class="col-xl-9"><div class="row g-3"><div class="col-xl-7">
            <div class="content-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="section-title mb-0">Page canvas</h2>
                    <span class="small text-muted" id="page-builder-status" role="status">Order saves automatically.</span>
                </div>
                
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 p-2 rounded bg-light">
                        <div class="d-flex align-items-center gap-2">
                            <span class="small fw-semibold">Live page preview</span>
                            <span class="small text-muted">Rendered with the original Piller frontend</span>
                        </div>
                        <div class="btn-group btn-group-sm" role="group" aria-label="Preview device size">
                            <button type="button" class="btn btn-outline-secondary active" data-preview-width="100%">Desktop</button>
                            <button type="button" class="btn btn-outline-secondary" data-preview-width="768px">Tablet</button>
                            <button type="button" class="btn btn-outline-secondary" data-preview-width="390px">Mobile</button>
                            <button type="button" class="btn btn-outline-secondary" id="refresh-page-preview" title="Refresh preview"><i class="fa-solid fa-rotate"></i></button>
                        </div>
                    </div>
                    <div class="border rounded bg-secondary-subtle p-2 mb-4" style="overflow:auto">
                        <div id="page-builder-preview-frame" class="mx-auto bg-white shadow-sm" style="width:100%;max-width:100%;transition:width .2s ease">
                            <iframe id="page-builder-preview" title="Rendered page preview"
                                src="{{ route('admin.pages.preview', $page) }}"
                                style="display:block;width:100%;height:720px;border:0;background:#fff"
                                loading="eager"></iframe>
                        </div>
                    </div>
                </div>
                </div>
                <div class="col-xl-5">
                    <div class="content-card">
                        <h2 class="section-title">Section inspector</h2>
                        <p class="small text-muted">Click a section in the rendered preview to open its settings here. Save each section after editing.</p>
                        <div id="page-builder-list" data-sort-url="{{ route('admin.pages.widgets.reorder', $page) }}" style="max-height:1000px;overflow:auto;padding-right:4px">
                    @forelse ($page->sections as $section)
                        @php
                            $definition = $widgetDefinitions[$section->section_key] ?? null;
                            $config = array_merge($definition['defaults'] ?? [], $section->config ?? []);
                            $querySchema = $queryableTypes[$section->section_type] ?? null;
                            $dataSource = $section->data_source ?? [];
                        @endphp
                        <article class="border rounded p-3 mb-3 page-builder-item" draggable="{{ $page->status === 'draft' ? 'true' : 'false' }}" data-section-id="{{ $section->id }}" data-section-key="{{ $section->section_key }}">
                            <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-muted" aria-hidden="true">⠿</span>
                                    <div>
                                        <strong>{{ $definition['label'] ?? $section->section_key }}</strong>
                                        <div class="small text-muted">{{ $section->source_design }} · {{ $section->section_type }}</div>
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap gap-1">
                                    <form method="POST" action="{{ route('admin.pages.widgets.duplicate', [$page, $section]) }}">@csrf
                                        <button class="btn btn-sm btn-outline-secondary" {{ $page->status !== 'draft' ? 'disabled' : '' }}>Duplicate</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.pages.widgets.toggle', [$page, $section]) }}">@csrf @method('PATCH')
                                        <button class="btn btn-sm btn-outline-secondary" {{ $page->status !== 'draft' ? 'disabled' : '' }}>{{ $section->is_enabled ? 'Disable' : 'Enable' }}</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.pages.widgets.delete', [$page, $section]) }}" onsubmit="return confirm('Remove this widget?')">@csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" {{ $page->status !== 'draft' ? 'disabled' : '' }}>Delete</button>
                                    </form>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('admin.pages.widgets.update', [$page, $section]) }}" data-widget-save>
                                @csrf @method('PATCH')
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Original Piller design</label>
                                    <select class="form-select form-select-sm" name="section_key">
                                        @foreach ($widgetDefinitions as $designKey => $designDefinition)
                                            @if (($designDefinition['type'] ?? null) === $section->section_type)
                                                <option value="{{ $designKey }}" @selected($section->section_key === $designKey)>{{ $designDefinition['label'] ?? $designKey }} · {{ $designDefinition['source_design'] ?? 'Piller' }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Switches to another registered design in this widget family. Matching content fields are retained; new fields use that design's defaults.</small>
                                </div>
                                @if (!empty($definition['fields']))
                                    <div class="row g-3">
                                        @foreach ($definition['fields'] as $field => $schema)
                                            @php $value = old('config.'.$field, $config[$field] ?? ''); @endphp
                                            <div class="{{ in_array($schema['type'], ['textarea'], true) ? 'col-12' : 'col-md-6' }}">
                                                <label class="form-label small">{{ $schema['label'] }}</label>
                                                @if ($schema['type'] === 'textarea')
                                                    <textarea class="form-control form-control-sm" name="config[{{ $field }}]" rows="2" maxlength="{{ $schema['max'] ?? 1500 }}">{{ $value }}</textarea>
                                                @elseif ($schema['type'] === 'select')
                                                    <select class="form-select form-select-sm" name="config[{{ $field }}]">
                                                        @foreach ($schema['options'] ?? [] as $option => $label)
                                                            <option value="{{ $option }}" @selected((string)$value === (string)$option)>{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                @elseif ($schema['type'] === 'image')
                                                    @php
                                                        $selectedMedia = $section->mediaAttachments->firstWhere('field_key', $field)?->media
                                                            ?? $mediaLibrary->firstWhere('id', (int) $value);
                                                        $imagePreview = $selectedMedia?->url ?: (\Illuminate\Support\Str::startsWith((string) $value, ['http://', 'https://', '/']) ? $value : ($value ? asset($value) : ''));
                                                    @endphp
                                                    <div class="d-flex gap-2 align-items-center mb-2">
                                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                            data-open-media-picker
                                                            data-target="image-value-{{ $section->id }}-{{ $field }}">Choose from Media Library</button>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                                            data-clear-media
                                                            data-target="image-value-{{ $section->id }}-{{ $field }}">Remove</button>
                                                    </div>
                                                    <input id="image-value-{{ $section->id }}-{{ $field }}" class="form-control form-control-sm mb-2" type="text"
                                                        name="config[{{ $field }}]" value="{{ $value }}" placeholder="Theme asset path or image URL">
                                                    <div class="border rounded p-2 bg-light" style="min-height:76px">
                                                        <img id="image-preview-{{ $section->id }}-{{ $field }}" data-media-preview src="{{ $imagePreview }}" alt="Selected image preview"
                                                            style="{{ $imagePreview ? 'display:block' : 'display:none' }};max-width:100%;max-height:130px;object-fit:contain">
                                                        <span class="small text-muted" data-empty-image="{{ $section->id }}-{{ $field }}" style="{{ $imagePreview ? 'display:none' : '' }}">No image selected</span>
                                                    </div>
                                                    <small class="text-muted">Select an uploaded image or keep an existing Piller theme asset path.</small>
                                                    <a class="small d-inline-block mt-1" href="{{ route('admin.media.index') }}" target="_blank" rel="noopener">Open Media Library to upload or manage images</a>
                                                @elseif ($schema['type'] === 'checkbox')
                                                    <input type="hidden" name="config[{{ $field }}]" value="0">
                                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="config[{{ $field }}]" value="1" @checked((bool)$value)><span class="form-check-label">Enabled</span></div>
                                                @else
                                                    <input class="form-control form-control-sm" name="config[{{ $field }}]" type="{{ in_array($schema['type'], ['number','color','url'], true) ? ($schema['type'] === 'url' ? 'url' : $schema['type']) : 'text' }}" value="{{ $value }}" @if(isset($schema['max']) && $schema['type'] !== 'number') maxlength="{{ $schema['max'] }}" @endif @if($schema['type'] === 'number') min="{{ $schema['min'] ?? 0 }}" max="{{ $schema['max'] ?? 999999 }}" @endif>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                @if ($querySchema)
                                    <hr>
                                    <h3 class="h6">Database query</h3>
                                    <div class="row g-2">
                                        <div class="col-md-3"><label class="form-label small">Items limit</label><input class="form-control form-control-sm" type="number" min="1" max="24" name="data_source[limit]" value="{{ $dataSource['limit'] ?? ($config['items_limit'] ?? 6) }}"></div>
                                        <div class="col-md-4"><label class="form-label small">Sort by</label><select class="form-select form-select-sm" name="data_source[sort_by]">@foreach($querySchema['sort'] as $sort)<option value="{{ $sort }}" @selected(($dataSource['sort_by'] ?? 'published_at') === $sort)>{{ ucfirst(str_replace('_',' ',$sort)) }}</option>@endforeach</select></div>
                                        <div class="col-md-2"><label class="form-label small">Direction</label><select class="form-select form-select-sm" name="data_source[sort_direction]"><option value="desc" @selected(($dataSource['sort_direction'] ?? 'desc') === 'desc')>DESC</option><option value="asc" @selected(($dataSource['sort_direction'] ?? '') === 'asc')>ASC</option></select></div>
                                        <div class="col-md-3"><label class="form-label small">Columns</label><input class="form-control form-control-sm" type="number" min="1" max="6" name="data_source[columns]" value="{{ $dataSource['columns'] ?? ($config['columns'] ?? 3) }}"></div>
                                        <div class="col-md-6"><label class="form-label small">Only these record IDs (comma separated, optional)</label><input class="form-control form-control-sm" name="data_source[selected_ids]" value="{{ implode(',', $dataSource['selected_ids'] ?? []) }}" placeholder="e.g. 2,5,8"></div>
                                        @if($querySchema['slider'] ?? true)<div class="col-md-3 pt-4"><input type="hidden" name="data_source[slider]" value="0"><div class="form-check"><input class="form-check-input" type="checkbox" name="data_source[slider]" value="1" @checked($dataSource['slider'] ?? true)><label class="form-check-label small">Enable slider</label></div></div>@endif
                                    </div>
                                @endif
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="small text-muted">{{ $section->is_enabled ? 'Visible on frontend' : 'Hidden on frontend' }}</span>
                                    <button class="btn btn-sm btn-primary" {{ $page->status !== 'draft' ? 'disabled' : '' }}>Save widget settings</button>
                                </div>
                            </form>
                        </article>
                    @empty
                        <div class="text-center text-muted py-5 border rounded">This page has no widgets yet. Choose one from the library.</div>
                    @endforelse
                </div>
            </div>
        </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const previewFrame = document.getElementById('page-builder-preview');
    const previewWrap = document.getElementById('page-builder-preview-frame');
    document.querySelectorAll('[data-preview-width]').forEach(button => {
        button.addEventListener('click', () => {
            document.querySelectorAll('[data-preview-width]').forEach(item => item.classList.remove('active'));
            button.classList.add('active');
            previewWrap.style.width = button.dataset.previewWidth;
        });
    });
    const selectSection = id => {
        document.querySelectorAll('.page-builder-item').forEach(item => {
            item.classList.toggle('border-primary', item.dataset.sectionId === String(id));
            item.classList.toggle('shadow-sm', item.dataset.sectionId === String(id));
        });
        const inspectorItem = document.querySelector('.page-builder-item[data-section-id="' + id + '"]');
        inspectorItem?.scrollIntoView({behavior: 'smooth', block: 'nearest'});
    };
    const bindPreviewSections = () => {
        try {
            const previewDocument = previewFrame.contentDocument;
            previewDocument?.querySelectorAll('.cms-widget-instance').forEach(section => {
                section.style.cursor = 'pointer';
                section.title = 'Click to edit this section';
                section.addEventListener('click', event => {
                    const sectionId = section.id.replace('cms-widget-', '');
                    if (!document.querySelector('.page-builder-item[data-section-id="' + sectionId + '"]')) return;
                    event.preventDefault();
                    event.stopPropagation();
                    selectSection(sectionId);
                }, true);
            });
        } catch (error) {
            // Preview remains usable even if the browser blocks same-origin frame access.
        }
    };
    previewFrame.addEventListener('load', bindPreviewSections);
    document.querySelectorAll('.page-builder-item').forEach(item => {
        item.addEventListener('click', event => {
            if (event.target.closest('button, input, select, textarea, a, form')) return;
            selectSection(item.dataset.sectionId);
        });
    });
    document.getElementById('refresh-page-preview')?.addEventListener('click', () => {
        previewFrame.src = previewFrame.src;
    });
    document.querySelectorAll('[data-media-select]').forEach(select => {
        select.addEventListener('change', () => {
            const option = select.selectedOptions[0];
            const input = document.getElementById(select.dataset.input);
            const image = document.getElementById(select.dataset.target);
            const empty = document.querySelector('[data-empty-image="' + select.dataset.target.replace('image-preview-', '') + '"]');
            input.value = select.value;
            image.src = option?.dataset.url || '';
            image.style.display = image.src ? 'block' : 'none';
            if (empty) empty.style.display = image.src ? 'none' : 'inline';
        });
    });
    document.querySelectorAll('[data-clear-image]').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.dataset.clearImage;
            const input = document.getElementById('image-value-' + id);
            const image = document.getElementById('image-preview-' + id);
            const select = button.parentElement.querySelector('[data-media-select]');
            input.value = '';
            select.value = '';
            image.removeAttribute('src');
            image.style.display = 'none';
            const empty = button.closest('.col-12, .col-md-6, .col-md-3, .col-md-4')?.querySelector('[data-empty-image]');
            if (empty) empty.style.display = 'inline';
        });
    });
    document.getElementById('widget-library-search')?.addEventListener('input', event => {
        const term = event.target.value.trim().toLowerCase();
        document.querySelectorAll('.widget-library-item').forEach(item => {
            item.hidden = !item.dataset.search.includes(term);
        });
    });
    document.querySelectorAll('form[data-widget-save]').forEach(form => {
        form.addEventListener('submit', async event => {
            event.preventDefault();
            const submitButton = form.querySelector('button[type="submit"], button:not([type])');
            const originalLabel = submitButton?.textContent;
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = 'Saving…';
            }
            const status = document.getElementById('page-builder-status');
            if (status) status.textContent = 'Saving section settings…';
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const payload = await response.json().catch(() => ({}));
                if (!response.ok) {
                    const messages = Object.values(payload.errors || {}).flat();
                    throw new Error(messages[0] || payload.message || 'Could not save this section.');
                }
                const article = form.closest('.page-builder-item');
                const selectedDesign = form.querySelector('[name="section_key"]')?.value;
                if (article && selectedDesign && selectedDesign !== article.dataset.sectionKey) {
                    window.location.reload();
                    return;
                }
                if (previewFrame) {
                    const previewUrl = new URL(previewFrame.src, window.location.origin);
                    previewUrl.searchParams.set('_preview', String(Date.now()));
                    previewFrame.src = previewUrl.toString();
                }
                if (status) status.textContent = 'Settings saved. Frontend preview refreshed.';
            } catch (error) {
                if (status) status.textContent = error.message;
            } finally {
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.textContent = originalLabel || 'Save widget settings';
                }
            }
        });
    });
    const list = document.getElementById('page-builder-list');
    if (!list) return;
    let dragged = null;
    const status = document.getElementById('page-builder-status');
    list.querySelectorAll('.page-builder-item[draggable="true"]').forEach(item => {
        item.addEventListener('dragstart', () => { dragged = item; item.classList.add('opacity-50'); });
        item.addEventListener('dragend', async () => {
            if (!dragged) return;
            dragged.classList.remove('opacity-50');
            dragged = null;
            const ids = [...list.querySelectorAll('.page-builder-item')].map(node => Number(node.dataset.sectionId));
            status.textContent = 'Saving order…';
            try {
                const response = await fetch(list.dataset.sortUrl, {
                    method: 'PATCH',
                    headers: {'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content || ''},
                    body: JSON.stringify({sections: ids})
                });
                if (!response.ok) throw new Error('Could not save order');
                status.textContent = 'Order saved.';
            } catch (error) { status.textContent = error.message; }
        });
        item.addEventListener('dragover', event => {
            event.preventDefault();
            if (!dragged || dragged === item) return;
            const rect = item.getBoundingClientRect();
            const after = event.clientY > rect.top + rect.height / 2;
            list.insertBefore(dragged, after ? item.nextSibling : item);
        });
    });
});
</script>
@endpush
