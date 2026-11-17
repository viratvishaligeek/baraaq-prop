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
        <div class="col-xl-4">
            <div class="content-card">
                <h2 class="section-title">Widget library</h2>
                <p class="small text-muted">Choose a widget and one of its registered Piller designs. The design controls the original theme markup; the instance settings control its content and query.</p>
                @foreach ($widgetGroups as $group => $widgets)
                    <h3 class="h6 mt-3">{{ $group }}</h3>
                    @foreach ($widgets as $widget)
                        <form method="POST" action="{{ route('admin.pages.widgets.add', $page) }}" class="border rounded p-2 mb-2 d-flex align-items-center justify-content-between gap-2">
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

        <div class="col-xl-8">
            <div class="content-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="section-title mb-0">Page canvas</h2>
                    <span class="small text-muted" id="page-builder-status" role="status">Order saves automatically.</span>
                </div>
                <div id="page-builder-list" data-sort-url="{{ route('admin.pages.widgets.reorder', $page) }}">
                    @forelse ($page->sections as $section)
                        @php
                            $definition = config('cms-sections.sections', [])[$section->section_key] ?? null;
                            $config = array_merge($definition['defaults'] ?? [], $section->config ?? []);
                            $querySchema = $queryableTypes[$section->section_type] ?? null;
                            $dataSource = $section->data_source ?? [];
                        @endphp
                        <article class="border rounded p-3 mb-3 page-builder-item" draggable="{{ $page->status === 'draft' ? 'true' : 'false' }}" data-section-id="{{ $section->id }}">
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

                            <form method="POST" action="{{ route('admin.pages.widgets.update', [$page, $section]) }}">
                                @csrf @method('PATCH')
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
                                        <div class="col-md-3 pt-4"><input type="hidden" name="data_source[slider]" value="0"><div class="form-check"><input class="form-check-input" type="checkbox" name="data_source[slider]" value="1" @checked($dataSource['slider'] ?? true)><label class="form-check-label small">Enable slider</label></div></div>
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
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
