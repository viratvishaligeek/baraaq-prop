@extends('admin.layouts.app')
@section('title', $menu->exists ? 'Edit menu' : 'Create menu')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">{{ $menu->exists ? 'Edit menu' : 'Create menu' }}</h1>
            <p class="text-muted mb-0">Drag to reorder; choose a parent to nest an item. Dynamic targets follow published CMS
                content.</p>
        </div>
        <a href="{{ route('admin.menus.index') }}" class="btn btn-light">Back to menus</a>
    </div>
    <form method="POST" action="{{ $menu->exists ? route('admin.menus.update', $menu) : route('admin.menus.store') }}">@csrf
        @if ($menu->exists)
            @method('PUT')
        @endif
        <div class="content-card mb-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Menu name</label>
                    <input class="form-control" name="name" value="{{ old('name', $menu->name) }}" maxlength="120"
                        required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Location key</label>
                    <input class="form-control" name="location" value="{{ old('location', $menu->location) }}"
                        placeholder="primary or footer" maxlength="80" required>
                    <div class="form-text">Use primary for the main header menu and footer for the footer menu.</div>
                </div>
            </div>
        </div>
        <div class="content-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="section-title mb-0">Menu items</h2>
                <button type="button" class="btn btn-sm btn-outline-primary" id="add-menu-item">Add item</button>
            </div>
            <div id="menu-items-list">
                @php
                    $rows = old(
                        'items',
                        $menuItems
                            ->map(
                                fn($item) => [
                                    'label' => $item->label,
                                    'url' => $item->url,
                                    'link_type' => $item->link_type ?? 'custom',
                                    'link_id' => $item->link_id,
                                    'target' => $item->target,
                                    'parent_index' => $item->parent_index,
                                    'is_active' => $item->is_active ? 1 : 0,
                                ],
                            )
                            ->all(),
                    );

                @endphp
                @foreach ($rows as $index => $item)
                    <div class="menu-item-row border rounded p-3 mb-3" draggable="true" data-index="{{ $index }}">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-semibold menu-item-handle" style="cursor:move">↕ Drag to reorder · Item <span
                                    class="item-number">{{ $index + 1 }}</span>
                            </span>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-menu-item">Remove</button>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label">Label</label>
                                <input class="form-control" name="items[{{ $index }}][label]"
                                    value="{{ $item['label'] ?? '' }}" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Link type</label>
                                <select class="form-select link-type" name="items[{{ $index }}][link_type]">
                                    <option value="custom" @selected(($item['link_type'] ?? 'custom') === 'custom')>Custom URL</option>
                                    <option value="page" @selected(($item['link_type'] ?? '') === 'page')>Page</option>
                                    <option value="blog_category" @selected(($item['link_type'] ?? '') === 'blog_category')>Blog category</option>
                                    <option value="property_category" @selected(($item['link_type'] ?? '') === 'property_category')>Property category
                                    </option>
                                    <option value="service" @selected(($item['link_type'] ?? '') === 'service')>Services</option>
                                </select>
                            </div>
                            <div class="col-md-3 custom-url">
                                <label class="form-label">URL</label>
                                <input class="form-control" name="items[{{ $index }}][url]"
                                    value="{{ $item['url'] ?? '' }}" placeholder="/contact or https://...">
                            </div>
                            <div class="col-md-2 dynamic-target">
                                <label class="form-label">Content target</label>
                                <select class="form-select link-id" name="items[{{ $index }}][link_id]">
                                    <option value="">Choose...</option>
                                    @foreach ($pages as $record)
                                        <option data-type="page" value="{{ $record->id }}" @selected(($item['link_type'] ?? '') === 'page' && (string) ($item['link_id'] ?? '') === (string) $record->id)>
                                            Page: {{ $record->title }}</option>
                                    @endforeach
                                    @foreach ($blogCategories as $record)
                                        <option data-type="blog_category" value="{{ $record->id }}"
                                            @selected(($item['link_type'] ?? '') === 'blog_category' && (string) ($item['link_id'] ?? '') === (string) $record->id)>Blog: {{ $record->name }}</option>
                                    @endforeach
                                    @foreach ($propertyCategories as $record)
                                        <option data-type="property_category" value="{{ $record->id }}"
                                            @selected(($item['link_type'] ?? '') === 'property_category' && (string) ($item['link_id'] ?? '') === (string) $record->id)>Property: {{ $record->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">Parent</label>
                                <select class="form-select parent-select" name="items[{{ $index }}][parent_index]">
                                    <option value="">Top</option>
                                    @foreach ($rows as $parentIndex => $parent)
                                        @if ($parentIndex < $index)
                                            <option value="{{ $parentIndex }}" @selected((string) ($item['parent_index'] ?? '') === (string) $parentIndex)>
                                                {{ $parent['label'] }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">Target</label>
                                <select class="form-select" name="items[{{ $index }}][target]">
                                    <option value="_self" @selected(($item['target'] ?? '_self') === '_self')>Same</option>
                                    <option value="_blank" @selected(($item['target'] ?? '') === '_blank')>New</option>
                                </select>
                                <label class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox"
                                        name="items[{{ $index }}][is_active]" value="1"
                                        @checked($item['is_active'] ?? true)>
                                    <span class="form-check-label small">On</span>
                                </label>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <button class="btn btn-primary mt-3">Save menu</button>
        </div>
    </form>
@endsection
@push('scripts')
    <script>
        (function($) {
            let dragged = null;

            function configure(row) {
                const type = row.find('.link-type').val();
                row.find('.custom-url').toggle(type === 'custom');
                row.find('.dynamic-target').toggle(['page', 'blog_category', 'property_category'].includes(type));
                row.find('.link-id option').each(function() {
                    const option = $(this);
                    option.toggle(!option.data('type') || option.data('type') === type);
                });
            }

            function renumber() {
                const rows = $('#menu-items-list .menu-item-row').toArray(),
                    oldParents = [];
                rows.forEach(function(row) {
                    oldParents.push($(row).find('.parent-select').val());
                });
                rows.forEach(function(row, index) {
                    const item = $(row);
                    item.attr('data-index', index).find('.item-number').text(index + 1);
                    item.find('[name]').each(function() {
                        this.name = this.name.replace(/items\[\d+\]/, 'items[' + index + ']');
                    });
                    const select = item.find('.parent-select'),
                        old = oldParents[index];
                    select.empty().append($('<option>', {
                        value: '',
                        text: 'Top'
                    }));
                    rows.forEach(function(parent, parentIndex) {
                        if (parentIndex < index) select.append($('<option>', {
                            value: parentIndex,
                            text: $(parent).find('input[name$="[label]"]').val() ||
                                'Item ' + (parentIndex + 1)
                        }));
                    });
                    select.val(old);
                    configure(item);
                });
            }
            $('#add-menu-item').on('click', function() {
                const i = $('#menu-items-list .menu-item-row').length;
                const row = $(`<div class="menu-item-row border rounded p-3 mb-3" draggable="true">
<div class="d-flex justify-content-between mb-2">
<span class="fw-semibold">↕ Drag to reorder · Item <span class="item-number">${i+1}</span>
</span>
<button type="button" class="btn btn-sm btn-outline-danger remove-menu-item">Remove</button>
</div>
<div class="row g-2">
<div class="col-md-3">
<label class="form-label">Label</label>
<input class="form-control" name="items[${i}][label]" required>
</div>
<div class="col-md-2">
<label class="form-label">Link type</label>
<select class="form-select link-type" name="items[${i}][link_type]">
<option value="custom">Custom URL</option>
<option value="page">Page</option>
<option value="blog_category">Blog category</option>
<option value="property_category">Property category</option>
<option value="service">Services</option>
</select>
</div>
<div class="col-md-3 custom-url">
<label class="form-label">URL</label>
<input class="form-control" name="items[${i}][url]" placeholder="/contact or https://...">
</div>
<div class="col-md-2 dynamic-target">
<label class="form-label">Content target</label>
<select class="form-select link-id" name="items[${i}][link_id]">
<option value="">Choose...</option>@foreach ($pages as $record)<option data-type="page" value="{{ $record->id }}">Page: {{ $record->title }}</option>@endforeach
@foreach ($blogCategories as $record)<option data-type="blog_category" value="{{ $record->id }}">Blog: {{ $record->name }}</option>@endforeach
@foreach ($propertyCategories as $record)<option data-type="property_category" value="{{ $record->id }}">Property: {{ $record->name }}</option>@endforeach</select>
</div>
<div class="col-md-1">
<label class="form-label">Parent</label>
<select class="form-select parent-select" name="items[${i}][parent_index]">
<option value="">Top</option>
</select>
</div>
<div class="col-md-1">
<label class="form-label">Target</label>
<select class="form-select" name="items[${i}][target]">
<option value="_self">Same</option>
<option value="_blank">New</option>
</select>
<label class="form-check mt-2">
<input class="form-check-input" type="checkbox" name="items[${i}][is_active]" value="1" checked>
<span class="form-check-label small">On</span>
</label>
</div>
</div>
</div>`);
                $('#menu-items-list').append(row);
                renumber();
            });
            $('#menu-items-list').on('click', '.remove-menu-item', function() {
                $(this).closest('.menu-item-row').remove();
                renumber();
            });
            $('#menu-items-list').on('change', '.link-type', function() {
                configure($(this).closest('.menu-item-row'));
            });
            $('#menu-items-list').on('input', 'input[name$="[label]"]', renumber);
            $('#menu-items-list').on('dragstart', '.menu-item-row', function(e) {
                dragged = this;
                e.originalEvent.dataTransfer.effectAllowed = 'move';
                $(this).addClass('opacity-50');
            });
            $('#menu-items-list').on('dragover', '.menu-item-row', function(e) {
                e.preventDefault();
                if (dragged && dragged !== this) $(this).before(dragged);
            });
            $('#menu-items-list').on('dragend', '.menu-item-row', function() {
                $('.menu-item-row').removeClass('opacity-50');
                dragged = null;
                renumber();
            });
            renumber();
        })(jQuery);
    </script>
@endpush
