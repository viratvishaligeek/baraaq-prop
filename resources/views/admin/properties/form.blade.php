@extends('admin.layouts.app')
@section('title', $property->exists ? 'Edit property' : 'Add property')
@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.properties.index') }}" class="text-decoration-none small">← Properties</a>
        <h1 class="page-title mt-2">{{ $property->exists ? 'Edit property' : 'Add property' }}</h1>
    </div>
    <form method="POST" enctype="multipart/form-data"
        action="{{ $property->exists ? route('admin.properties.update', $property) : route('admin.properties.store') }}">
        @csrf
        @if ($property->exists)
            @method('PUT')
        @endif
        <div class="row g-4">
            <div class="col-xl-8">
                <div class="content-card">
                    <h2 class="section-title">Property information</h2>
                    <label class="form-label">Title *</label>
                    <input class="form-control mb-3" name="title" value="{{ old('title', $property->title) }}" required>
                    @error('title')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                    <label class="form-label">
                        Description</label>
                    <textarea class="form-control mb-3" rows="8" name="description" data-rich-editor>{{ old('description', $property->description) }}</textarea>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Property type</label>
                            <input class="form-control" name="property_type"
                                value="{{ old('property_type', $property->property_type) }}"
                                placeholder="Apartment, Villa, Plot">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select class="form-select" name="category_id">
                                <option value="">Uncategorized</option>
                                @foreach ($categories as $c)
                                    <option value="{{ $c->id }}" @selected(old('category_id', $property->category_id) == $c->id)>{{ $c->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="content-card mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div><h2 class="section-title mb-1">Accommodation & spaces</h2><p class="text-muted small mb-0">Add any number of rooms, parking spaces and custom areas.</p></div>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="add-accommodation">Add space</button>
                    </div>
                    <div id="accommodation-list">
                        @php($spaces = old('accommodations', $property->exists ? $property->accommodations->map(fn($space) => $space->toArray())->all() : []))
                        @foreach ($spaces as $i => $space)
                            <div class="border rounded p-3 mb-3 accommodation-item">
                                <div class="d-flex justify-content-between mb-2"><strong>Space <span class="space-number">{{ $i + 1 }}</span></strong><button type="button" class="btn btn-sm btn-outline-danger remove-repeater">Remove</button></div>
                                <div class="row g-2">
                                    <div class="col-md-5"><label class="form-label">Name *</label><input class="form-control" name="accommodations[{{ $i }}][name]" value="{{ $space['name'] ?? '' }}" required></div>
                                    <div class="col-md-4"><label class="form-label">Category</label><input class="form-control" name="accommodations[{{ $i }}][category]" value="{{ $space['category'] ?? '' }}" placeholder="Bedroom, Bathroom, Parking"></div>
                                    <div class="col-md-3"><label class="form-label">Area</label><input type="number" min="0" step=".01" class="form-control" name="accommodations[{{ $i }}][area]" value="{{ $space['area'] ?? '' }}"></div>
                                    <div class="col-md-3"><label class="form-label">Unit</label><input class="form-control" name="accommodations[{{ $i }}][area_unit]" value="{{ $space['area_unit'] ?? 'sqft' }}"></div>
                                    <div class="col-md-9"><label class="form-label">Description</label><input class="form-control" name="accommodations[{{ $i }}][description]" value="{{ $space['description'] ?? '' }}"></div>
                                    <div class="col-12"><label class="form-check"><input class="form-check-input" type="checkbox" value="1" name="accommodations[{{ $i }}][is_included]" @checked($space['is_included'] ?? true)> <span class="form-check-label">Included with property</span></label></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="content-card mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-3"><div><h2 class="section-title mb-1">Property FAQs</h2><p class="text-muted small mb-0">Questions and answers displayed with this property.</p></div><button type="button" class="btn btn-sm btn-outline-primary" id="add-faq">Add FAQ</button></div>
                    <div id="faq-list">
                        @php($faqs = old('faqs', $property->exists ? $property->faqs->map(fn($faq) => $faq->toArray())->all() : []))
                        @foreach ($faqs as $i => $faq)
                            <div class="border rounded p-3 mb-3 faq-item">
                                <div class="d-flex justify-content-between mb-2"><strong>FAQ <span class="faq-number">{{ $i + 1 }}</span></strong><button type="button" class="btn btn-sm btn-outline-danger remove-repeater">Remove</button></div>
                                <label class="form-label">Question *</label><input class="form-control mb-2" name="faqs[{{ $i }}][question]" value="{{ $faq['question'] ?? '' }}" required>
                                <label class="form-label">Answer *</label><textarea class="form-control" rows="3" name="faqs[{{ $i }}][answer]" required>{{ $faq['answer'] ?? '' }}</textarea>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="content-card mt-4">
                    <h2 class="section-title">Location</h2>
                    <label class="form-label">Address</label>
                    <input class="form-control mb-3" name="address" value="{{ old('address', $property->address) }}">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">City</label>
                            <input class="form-control" name="city" value="{{ old('city', $property->city) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">State</label>
                            <input class="form-control" name="state" value="{{ old('state', $property->state) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Postal code</label>
                            <input class="form-control" name="postal_code"
                                value="{{ old('postal_code', $property->postal_code) }}">
                        </div>
                    </div>
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label">Country</label>
                            <input class="form-control" name="country" value="{{ old('country', $property->country ?? 'India') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Availability status</label>
                            <select class="form-select" name="availability_status">
                                @foreach (['available' => 'Available', 'under_offer' => 'Under offer', 'sold' => 'Sold', 'rented' => 'Rented', 'unavailable' => 'Unavailable'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('availability_status', $property->availability_status ?? 'available') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="latitude">Latitude</label>
                            <input class="form-control" type="number" step="any" min="-90" max="90"
                                id="latitude" name="latitude" value="{{ old('latitude', $property->latitude) }}"
                                placeholder="e.g. 28.6139">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="longitude">Longitude</label>
                            <input class="form-control" type="number" step="any" min="-180" max="180"
                                id="longitude" name="longitude" value="{{ old('longitude', $property->longitude) }}"
                                placeholder="e.g. 77.2090">
                        </div>
                        <div class="col-12">
                            <small class="text-muted">Coordinates place this listing on the Home 3 property map. Leave both
                                blank to omit it from the map.</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="content-card">
                    <h2 class="section-title">Publish</h2>
                    <label class="form-label">Status</label>
                    @if (auth()->user()->isAdministrator() ||
                            auth()->user()->hasPermission('properties.manage') ||
                            auth()->user()->hasPermission('properties.publish'))
                        <select class="form-select mb-3" name="status">
                            <option value="draft" @selected(old('status', $property->status) === 'draft')>Draft</option>
                            <option value="published" @selected(old('status', $property->status) === 'published')>Published</option>
                            <option value="archived" @selected(old('status', $property->status) === 'archived')>Archived</option>
                        </select>
                    @else
                        <input type="hidden" name="status" value="{{ old('status', $property->status ?? 'draft') }}">
                        <div class="alert alert-light small">Current status:
                            {{ ucfirst(old('status', $property->status ?? 'draft')) }}. Publishing requires additional
                            permission.</div>
                    @endif
                    <label class="form-label">
                        Listing type</label>
                    <select class="form-select mb-3" name="listing_type">
                        <option value="sale" @selected(old('listing_type', $property->listing_type) === 'sale')>For sale</option>
                        <option value="rent" @selected(old('listing_type', $property->listing_type) === 'rent')>For rent</option>
                        <option value="lease" @selected(old('listing_type', $property->listing_type) === 'lease')>For lease</option>
                    </select>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="is_featured" value="1"
                            id="featured" @checked(old('is_featured', $property->is_featured))>
                        <label class="form-check-label" for="featured">Featured listing</label>
                    </div>
                    <button class="btn btn-primary w-100">Save property</button>
                </div>
                <div class="content-card mt-4">
                    <h2 class="section-title">Pricing & details</h2>
                    <label class="form-label">Price</label>
                    <input type="number" min="0" step=".01" class="form-control mb-3" name="price"
                        value="{{ old('price', $property->price) }}">
                    <label class="form-label">Price label</label>
                    <input class="form-control mb-3" name="price_label"
                        value="{{ old('price_label', $property->price_label) }}">
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label">Bedrooms</label>
                            <input type="number" min="0" class="form-control" name="bedrooms"
                                value="{{ old('bedrooms', $property->bedrooms) }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Bathrooms</label>
                            <input type="number" min="0" class="form-control" name="bathrooms"
                                value="{{ old('bathrooms', $property->bathrooms) }}">
                        </div>
                        <div class="col-8">
                            <label class="form-label">Area</label>
                            <input type="number" min="0" step=".01" class="form-control" name="area"
                                value="{{ old('area', $property->area) }}">
                        </div>
                        <div class="col-4">
                            <label class="form-label">Unit</label>
                            <input class="form-control" name="area_unit"
                                value="{{ old('area_unit', $property->area_unit ?: 'sqft') }}">
                        </div>
                    </div>
                </div>
                <div class="content-card mt-4">
                    <h2 class="section-title">Contact call-to-action</h2>
                    <label class="form-label" for="cta_mode">Enquiry option</label>
                    <select class="form-select mb-3" id="cta_mode" name="cta_mode">
                        <option value="both" @selected(old('cta_mode', $property->cta_mode ?? 'both') === 'both')>Contact form and WhatsApp</option>
                        <option value="contact_form" @selected(old('cta_mode', $property->cta_mode ?? 'both') === 'contact_form')>Contact form only</option>
                        <option value="whatsapp" @selected(old('cta_mode', $property->cta_mode ?? 'both') === 'whatsapp')>WhatsApp only</option>
                    </select>
                    <label class="form-label" for="whatsapp_number">Property WhatsApp number</label>
                    <input class="form-control mb-2" id="whatsapp_number" name="whatsapp_number"
                        value="{{ old('whatsapp_number', $property->whatsapp_number) }}"
                        placeholder="e.g. +919876543210">
                    <small class="text-muted d-block mb-3">Leave blank to use the global WhatsApp number configured in
                        Settings.</small>
                    <label class="form-label" for="whatsapp_message">WhatsApp message (optional)</label>
                    <textarea class="form-control" id="whatsapp_message" name="whatsapp_message" rows="3" maxlength="500">{{ old('whatsapp_message', $property->whatsapp_message) }}</textarea>
                </div>
                <div class="content-card mt-4">
                    <h2 class="section-title">SEO</h2>
                    <label class="form-label">URL slug</label>
                    <input class="form-control mb-3" name="slug" value="{{ old('slug', $property->slug) }}" placeholder="Generated from title">
                    <label class="form-label">SEO title</label>
                    <input class="form-control mb-3" name="meta_title"
                        value="{{ old('meta_title', $property->meta_title) }}">
                    <label class="form-label">SEO description</label>
                    <textarea class="form-control mb-3" name="meta_description">{{ old('meta_description', $property->meta_description) }}</textarea>
                    <label class="form-label">Focus keyword</label>
                    <input class="form-control mb-3" name="focus_keyword" value="{{ old('focus_keyword', $property->focus_keyword) }}">
                    <div class="border rounded p-3 bg-light mt-3" id="property-serp-preview">
                        <div class="small text-success" id="serp-url">{{ url('/properties/') }}/{{ $property->slug ?: 'property-slug' }}</div>
                        <div class="fw-semibold text-primary" id="serp-title">{{ $property->meta_title ?: $property->title ?: 'Property title' }}</div>
                        <div class="small text-muted" id="serp-description">{{ $property->meta_description ?: 'Add a meta description to preview your search result.' }}</div>
                        <div class="small text-muted mt-2"><span id="serp-title-count">0</span> / 60 title characters · <span id="serp-description-count">0</span> / 160 description characters</div>
                    </div>
                    <label class="form-label">Social sharing image</label>
                    <input class="form-control" type="file" name="og_image" id="upload-property-og-image" accept="image/*">
                    <input type="hidden" name="og_image_media_id" id="media-property-og-image" data-media-upload-input="upload-property-og-image">
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" data-open-media-picker data-target="media-property-og-image">Choose from Media Library</button>
                    <div class="mt-2"><img src="{{ $property->og_image ? Storage::disk('public')->url($property->og_image) : '' }}"
                        alt="Property social image preview" style="max-width:180px;max-height:100px;object-fit:cover"
                        data-media-preview @if (!$property->og_image) hidden @endif><span class="small text-muted" data-media-meta></span></div>
                </div>
                <div class="content-card mt-4">
                    <h2 class="section-title">Featured image</h2>
                    <input type="file" class="form-control" name="featured_image" id="upload-property-featured-image" accept="image/*">
                    <input type="hidden" name="featured_image_media_id" id="media-property-featured-image"
                        data-media-upload-input="upload-property-featured-image">
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" data-open-media-picker
                        data-target="media-property-featured-image">Choose from Media Library</button>
                    <div class="mt-2"><img class="img-fluid rounded" src="{{ $property->featured_image ? Storage::disk('public')->url($property->featured_image) : '' }}"
                        alt="Property featured image preview" style="max-width:100%;max-height:160px;object-fit:cover"
                        data-media-preview @if (!$property->featured_image) hidden @endif><span class="small text-muted" data-media-meta></span></div>
                    <small class="text-muted">Image up to 5 MB.</small>
                    <label class="form-label mt-3">Gallery images</label>
                    <input type="file" class="form-control" name="gallery[]" id="upload-property-gallery" accept="image/*" multiple data-preview-container-id="property-gallery-local-previews">
                    <div id="property-gallery-local-previews" class="d-flex flex-wrap gap-2 mt-2" aria-live="polite"></div>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2"
                        data-open-media-picker data-media-multiple data-target="property-gallery-media-selection">
                        Add images from Media Library
                    </button>
                    <div id="property-gallery-media-selection" data-input-name="gallery_media_ids[]" class="d-flex flex-wrap gap-2 mt-2"></div>
                    <small class="text-muted">Up to 20 gallery images total, 5 MB per uploaded file.</small>
                    @if ($property->exists && $property->images->isNotEmpty())
                        <p class="small text-muted mt-3 mb-2">Drag images to reorder. Use the star to set the featured
                            image.</p>
                        <div class="row g-3 mt-1" id="property-gallery">
                            @foreach ($property->images as $image)
                                <div class="col-6 gallery-image" draggable="true" data-image-id="{{ $image->id }}">
                                    <div class="border rounded p-2 h-100">
                                        <img class="img-fluid rounded mb-2"
                                            src="{{ Storage::disk('public')->url($image->path) }}"
                                            alt="{{ $image->alt_text }}">
                                        <input type="hidden" form="reorder-gallery" name="gallery_order[]"
                                            value="{{ $image->id }}">
                                        <div class="d-flex justify-content-between gap-1">
                                            @if (auth()->user()->isAdministrator() ||
                                                    auth()->user()->hasPermission('properties.manage') ||
                                                    auth()->user()->hasPermission('properties.edit'))
                                                <button type="submit" form="feature-image-{{ $image->id }}"
                                                    class="btn btn-sm {{ $property->featured_image === $image->path ? 'btn-warning' : 'btn-outline-secondary' }}"
                                                    title="Set featured image">★</button>
                                            @endif
                                            @if (auth()->user()->isAdministrator() ||
                                                    auth()->user()->hasPermission('properties.manage') ||
                                                    auth()->user()->hasPermission('properties.delete'))
                                                <button type="submit" form="delete-image-{{ $image->id }}"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Delete this gallery image?')">Delete</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if (auth()->user()->isAdministrator() ||
                                auth()->user()->hasPermission('properties.manage') ||
                                auth()->user()->hasPermission('properties.edit'))
                            <button type="submit" form="reorder-gallery"
                                class="btn btn-sm btn-outline-primary mt-3">Save gallery order</button>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </form>
    @if ($property->exists)
        <form id="reorder-gallery" method="POST" action="{{ route('admin.properties.images.reorder', $property) }}"
            class="d-none">
            @csrf @method('PATCH')</form>
        @foreach ($property->images as $image)
            <form id="delete-image-{{ $image->id }}" method="POST"
                action="{{ route('admin.properties.images.destroy', [$property, $image]) }}" class="d-none">@csrf
                @method('DELETE')</form>
            <form id="feature-image-{{ $image->id }}" method="POST"
                action="{{ route('admin.properties.images.feature', [$property, $image]) }}" class="d-none">@csrf
                @method('PATCH')</form>
        @endforeach
    @endif
    @push('scripts')
        <script>
            jQuery(function($) {
                let dragged = null;
                $('#property-gallery').on('dragstart', '.gallery-image', function(e) {
                    dragged = this;
                    e.originalEvent.dataTransfer.effectAllowed = 'move';
                    $(this).addClass('opacity-50')
                }).on('dragover', '.gallery-image', function(e) {
                    e.preventDefault();
                    if (dragged && dragged !== this) {
                        const box = this.getBoundingClientRect();
                        $(this)[e.originalEvent.clientY > box.top + box.height / 2 ? 'after' : 'before'](
                            dragged)
                    }
                }).on('dragend', '.gallery-image', function() {
                    $('.gallery-image').removeClass('opacity-50')
                });
            });
        </script>
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.tinymce) {
            tinymce.init({ selector: 'textarea[data-rich-editor]', license_key: 'gpl', menubar: 'edit insert format table', plugins: 'lists link image media table code searchreplace visualblocks wordcount anchor', toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image media table | blockquote | code', height: 420, branding: false, promotion: false, setup: editor => editor.on('change keyup undo redo', () => editor.save()) });
            document.querySelector('form')?.addEventListener('submit', () => tinymce.triggerSave());
        }
        const titleInput = document.querySelector('[name="title"]');
        const slugInput = document.querySelector('[name="slug"]');
        const seoTitle = document.querySelector('[name="meta_title"]');
        const seoDescription = document.querySelector('[name="meta_description"]');
        const serpTitle = document.getElementById('serp-title');
        const serpDescription = document.getElementById('serp-description');
        const serpUrl = document.getElementById('serp-url');
        const titleCount = document.getElementById('serp-title-count');
        const descriptionCount = document.getElementById('serp-description-count');
        let slugTouched = Boolean(slugInput?.value);
        const slugify = value => value.toString().normalize('NFKD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
        slugInput?.addEventListener('input', () => { slugTouched = true; });
        titleInput?.addEventListener('input', () => { if (!slugTouched && slugInput) slugInput.value = slugify(titleInput.value); refreshPreview(); });
        [slugInput, seoTitle, seoDescription].forEach(input => input?.addEventListener('input', refreshPreview));
        function refreshPreview() {
            const title = seoTitle?.value || titleInput?.value || 'Property title';
            const description = seoDescription?.value || 'Add a meta description to preview your search result.';
            if (serpTitle) serpTitle.textContent = title;
            if (serpDescription) serpDescription.textContent = description;
            if (serpUrl) serpUrl.textContent = window.location.origin + '/properties/' + (slugInput?.value || 'property-slug');
            if (titleCount) titleCount.textContent = title.length;
            if (descriptionCount) descriptionCount.textContent = description.length;
        }
        refreshPreview();
        let accommodationIndex = document.querySelectorAll('.accommodation-item').length;
        let faqIndex = document.querySelectorAll('.faq-item').length;
        const accommodationList = document.getElementById('accommodation-list');
        const faqList = document.getElementById('faq-list');
        document.getElementById('add-accommodation')?.addEventListener('click', () => {
            const i = accommodationIndex++;
            const wrapper = document.createElement('div');
            wrapper.className = 'border rounded p-3 mb-3 accommodation-item';
            wrapper.innerHTML = '<div class="d-flex justify-content-between mb-2"><strong>Space <span class="space-number"></span></strong><button type="button" class="btn btn-sm btn-outline-danger remove-repeater">Remove</button></div><div class="row g-2"><div class="col-md-5"><label class="form-label">Name *</label><input class="form-control" name="accommodations['+i+'][name]" required></div><div class="col-md-4"><label class="form-label">Category</label><input class="form-control" name="accommodations['+i+'][category]" placeholder="Bedroom, Bathroom, Parking"></div><div class="col-md-3"><label class="form-label">Area</label><input type="number" min="0" step=".01" class="form-control" name="accommodations['+i+'][area]"></div><div class="col-md-3"><label class="form-label">Unit</label><input class="form-control" name="accommodations['+i+'][area_unit]" value="sqft"></div><div class="col-md-9"><label class="form-label">Description</label><input class="form-control" name="accommodations['+i+'][description]"></div><div class="col-12"><label class="form-check"><input class="form-check-input" type="checkbox" value="1" checked name="accommodations['+i+'][is_included]"> <span class="form-check-label">Included with property</span></label></div></div>';
            accommodationList.append(wrapper); updateNumbers();
        });
        document.getElementById('add-faq')?.addEventListener('click', () => {
            const i = faqIndex++;
            const wrapper = document.createElement('div');
            wrapper.className = 'border rounded p-3 mb-3 faq-item';
            wrapper.innerHTML = '<div class="d-flex justify-content-between mb-2"><strong>FAQ <span class="faq-number"></span></strong><button type="button" class="btn btn-sm btn-outline-danger remove-repeater">Remove</button></div><label class="form-label">Question *</label><input class="form-control mb-2" name="faqs['+i+'][question]" required><label class="form-label">Answer *</label><textarea class="form-control" rows="3" name="faqs['+i+'][answer]" required></textarea>';
            faqList.append(wrapper); updateNumbers();
        });
        document.addEventListener('click', event => {
            const button = event.target.closest('.remove-repeater');
            if (!button) return;
            button.closest('.accommodation-item, .faq-item')?.remove(); updateNumbers();
        });
        function updateNumbers() {
            document.querySelectorAll('.accommodation-item').forEach((item, index) => item.querySelector('.space-number').textContent = index + 1);
            document.querySelectorAll('.faq-item').forEach((item, index) => item.querySelector('.faq-number').textContent = index + 1);
        }
    });
    </script>
    @endpush

@endsection
