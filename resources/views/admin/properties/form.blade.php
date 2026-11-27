@extends('admin.layouts.app')
@section('title', $property->exists ? 'Edit property' : 'Add property')
@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.properties.index') }}" class="text-decoration-none small">← Properties</a>
        <h1 class="page-title mt-2">{{ $property->exists ? 'Edit property' : 'Add property' }}</h1>
    </div>
    <form id="property-form" method="POST" enctype="multipart/form-data"
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
                            <div class="border rounded p-3 mb-3 accommodation-item" data-space-index="{{ $i }}">
                                <div class="d-flex justify-content-between mb-2"><strong>Space <span class="space-number">{{ $i + 1 }}</span></strong><div class="d-flex gap-1"><button type="button" class="btn btn-sm btn-light move-repeater-up" aria-label="Move space up">↑</button><button type="button" class="btn btn-sm btn-light move-repeater-down" aria-label="Move space down">↓</button><button type="button" class="btn btn-sm btn-outline-danger remove-repeater">Remove</button></div></div>
                                <div class="row g-2">
                                    <div class="col-md-5"><label class="form-label">Name *</label><input class="form-control" name="accommodations[{{ $i }}][name]" value="{{ $space['name'] ?? '' }}" required></div>
                                    <div class="col-md-4"><label class="form-label">Category</label><input class="form-control" name="accommodations[{{ $i }}][category]" value="{{ $space['category'] ?? '' }}" placeholder="Bedroom, Bathroom, Parking"></div>
                                    <div class="col-md-3"><label class="form-label">Area</label><input type="number" min="0" step=".01" class="form-control" name="accommodations[{{ $i }}][area]" value="{{ $space['area'] ?? '' }}"></div>
                                    <div class="col-md-3"><label class="form-label">Unit</label><input class="form-control" name="accommodations[{{ $i }}][area_unit]" value="{{ $space['area_unit'] ?? 'sqft' }}"></div>
                                    <div class="col-md-9"><label class="form-label">Description</label><input class="form-control" name="accommodations[{{ $i }}][description]" value="{{ $space['description'] ?? '' }}"></div>
                                    <div class="col-12"><label class="form-check"><input class="form-check-input" type="checkbox" value="1" name="accommodations[{{ $i }}][is_included]" @checked($space['is_included'] ?? true)> <span class="form-check-label">Included with property</span></label></div>
                                    @php($spaceMedia = $mediaItems->firstWhere('id', $space['image_media_id'] ?? null))
                                    <div class="col-12">
                                        <label class="form-label">Optional space image</label>
                                        <input type="hidden" name="accommodations[{{ $i }}][image_media_id]" id="media-accommodation-{{ $i }}" value="{{ $space['image_media_id'] ?? '' }}" data-remove-flag-id="remove-accommodation-image-{{ $i }}">
                                        <input type="hidden" name="accommodations[{{ $i }}][remove_image]" id="remove-accommodation-image-{{ $i }}" value="0">
                                        <div class="d-flex flex-wrap gap-2 mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-open-media-picker data-target="media-accommodation-{{ $i }}">Choose from Media Library</button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-clear-media data-target="media-accommodation-{{ $i }}">Remove selected image</button>
                                        </div>
                                        <div class="mt-2"><img class="rounded border" style="width:120px;height:80px;object-fit:cover" data-media-preview src="{{ $spaceMedia?->url ?? '' }}" alt="{{ $spaceMedia?->alt_text ?? 'Space image preview' }}" @if (!$spaceMedia) hidden @endif><span class="small text-muted" data-media-meta></span></div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2"><label class="form-label mb-0">Custom attributes</label><button type="button" class="btn btn-sm btn-outline-secondary add-space-attribute">Add attribute</button></div>
                                    <div class="space-attributes">
                                        @foreach (($space['attributes'] ?? []) as $attributeIndex => $attribute)
                                            @php($attributeKey = is_array($attribute) ? ($attribute['key'] ?? '') : $attributeIndex)
                                            @php($attributeValue = is_array($attribute) ? ($attribute['value'] ?? '') : $attribute)
                                            <div class="row g-2 mb-2 space-attribute">
                                                <div class="col-5"><input class="form-control form-control-sm" name="accommodations[{{ $i }}][attributes][{{ $attributeIndex }}][key]" value="{{ $attributeKey }}" placeholder="Attribute name" required></div>
                                                <div class="col-5"><input class="form-control form-control-sm" name="accommodations[{{ $i }}][attributes][{{ $attributeIndex }}][value]" value="{{ $attributeValue }}" placeholder="Value"></div>
                                                <div class="col-2"><button type="button" class="btn btn-sm btn-outline-danger remove-space-attribute">×</button></div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="content-card mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-3"><div><h2 class="section-title mb-1">Property amenities & features</h2><p class="text-muted small mb-0">Add reusable property features such as pool, garden, lift, gym or security.</p></div><button type="button" class="btn btn-sm btn-outline-primary" id="add-property-feature">Add feature</button></div>
                    <div id="property-feature-list">
                        @foreach (old('features', $property->features ?? []) as $featureIndex => $feature)
                            <div class="input-group mb-2 property-feature-item"><input class="form-control" name="features[]" value="{{ $feature }}" maxlength="120" placeholder="Feature or amenity" required><button type="button" class="btn btn-outline-danger remove-property-feature">Remove</button></div>
                        @endforeach
                    </div>
                </div>
                <div class="content-card mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-3"><div><h2 class="section-title mb-1">Property FAQs</h2><p class="text-muted small mb-0">Questions and answers displayed with this property.</p></div><button type="button" class="btn btn-sm btn-outline-primary" id="add-faq">Add FAQ</button></div>
                    <div id="faq-list">
                        @php($faqs = old('faqs', $property->exists ? $property->faqs->map(fn($faq) => $faq->toArray())->all() : []))
                        @foreach ($faqs as $i => $faq)
                            <div class="border rounded p-3 mb-3 faq-item">
                                <div class="d-flex justify-content-between mb-2"><strong>FAQ <span class="faq-number">{{ $i + 1 }}</span></strong><div class="d-flex gap-1"><button type="button" class="btn btn-sm btn-light move-repeater-up" aria-label="Move FAQ up">↑</button><button type="button" class="btn btn-sm btn-light move-repeater-down" aria-label="Move FAQ down">↓</button><button type="button" class="btn btn-sm btn-outline-danger remove-repeater">Remove</button></div></div>
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
                        <div class="col-12">
                            <label class="form-label" for="map_url">Google Maps Embed URL</label>
                            <input class="form-control" type="url" id="map_url" name="map_url"
                                value="{{ old('map_url', $property->map_url) }}"
                                placeholder="https://www.google.com/maps/embed?pb=...">
                            <div class="form-text">
                                Open Google Maps, choose Share → Embed a map → Copy HTML, then paste only the iframe src URL here.
                                This exact map will appear in the property details page. Existing latitude/longitude values are kept for legacy map features.
                            </div>
                            @error('map_url')<div class="text-danger small">{{ $message }}</div>@enderror
                            <details class="mt-3">
                                <summary class="small text-muted">Optional coordinates for legacy homepage map pins</summary>
                                <div class="row g-3 mt-1">
                                    <div class="col-md-6">
                                        <label class="form-label" for="latitude">Latitude</label>
                                        <input class="form-control" type="number" step="any" min="-90" max="90" id="latitude" name="latitude" value="{{ old('latitude', $property->latitude) }}" placeholder="e.g. 28.6139">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="longitude">Longitude</label>
                                        <input class="form-control" type="number" step="any" min="-180" max="180" id="longitude" name="longitude" value="{{ old('longitude', $property->longitude) }}" placeholder="e.g. 77.2090">
                                    </div>
                                </div>
                            </details>
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
                    <input class="form-control mb-3" name="slug" data-slug-source="[name=title]" value="{{ old('slug', $property->slug) }}" placeholder="Generated from title">
                    <label class="form-label">SEO title</label>
                    <input class="form-control mb-3" name="meta_title"
                        value="{{ old('meta_title', $property->meta_title) }}">
                    <label class="form-label">SEO description</label>
                    <textarea class="form-control mb-3" name="meta_description">{{ old('meta_description', $property->meta_description) }}</textarea>
                    <label class="form-label">Focus keyword</label>
                    <input class="form-control mb-3" name="focus_keyword" value="{{ old('focus_keyword', $property->focus_keyword) }}">
                    <div class="border rounded p-3 bg-light mt-3" id="property-serp-preview" data-seo-preview data-title-input="[name=title]" data-slug-input="[name=slug]" data-meta-title-input="[name=meta_title]" data-meta-description-input="[name=meta_description]" data-url-prefix="/properties/" data-url-output="#serp-url" data-title-output="#serp-title" data-description-output="#serp-description" data-title-count="#serp-title-count" data-description-count="#serp-description-count">
                        <div class="small text-success" id="serp-url">{{ url('/properties/') }}/{{ $property->slug ?: 'property-slug' }}</div>
                        <div class="fw-semibold text-primary" id="serp-title">{{ $property->meta_title ?: $property->title ?: 'Property title' }}</div>
                        <div class="small text-muted" id="serp-description">{{ $property->meta_description ?: 'Add a meta description to preview your search result.' }}</div>
                        <div class="small text-muted mt-2"><span id="serp-title-count">0</span> / 60 title characters · <span id="serp-description-count">0</span> / 160 description characters</div>
                    </div>
                    <label class="form-label">Social sharing image</label>
                    <input type="hidden" name="og_image_media_id" id="media-property-og-image" value="{{ old('og_image_media_id', $mediaItems->firstWhere('path', $property->og_image)?->id) }}" data-remove-flag-id="remove-property-og-image">
                    <input type="hidden" name="remove_og_image" id="remove-property-og-image" value="0">
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" data-open-media-picker data-target="media-property-og-image">Choose from Media Library</button>
                        <button type="button" class="btn btn-sm btn-outline-danger" data-clear-media data-target="media-property-og-image">Remove selected image</button>
                    </div>
                    <div class="mt-2"><img src="{{ $property->og_image ? Storage::disk('public')->url($property->og_image) : '' }}"
                        alt="Property social image preview" style="max-width:180px;max-height:100px;object-fit:cover"
                        data-media-preview @if (!$property->og_image) hidden @endif><span class="small text-muted" data-media-meta></span></div>
                </div>
                <div class="content-card mt-4">
                    <h2 class="section-title">Featured image</h2>
                    <input type="hidden" name="featured_image_media_id" id="media-property-featured-image"
                        value="{{ old('featured_image_media_id', $mediaItems->firstWhere('path', $property->featured_image)?->id) }}"
                        data-remove-flag-id="remove-property-featured-image">
                    <input type="hidden" name="remove_featured_image" id="remove-property-featured-image" value="0">
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" data-open-media-picker data-target="media-property-featured-image">Choose from Media Library</button>
                        <button type="button" class="btn btn-sm btn-outline-danger" data-clear-media data-target="media-property-featured-image">Remove selected image</button>
                    </div>
                    <div class="mt-2"><img class="img-fluid rounded" src="{{ $property->featured_image ? Storage::disk('public')->url($property->featured_image) : '' }}"
                        alt="Property featured image preview" style="max-width:100%;max-height:160px;object-fit:cover"
                        data-media-preview @if (!$property->featured_image) hidden @endif><span class="small text-muted" data-media-meta></span></div>
                    <small class="text-muted">Image up to 5 MB.</small>
                    <label class="form-label mt-3">Gallery images</label>
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
    
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        let accommodationIndex = document.querySelectorAll('.accommodation-item').length;
        let faqIndex = document.querySelectorAll('.faq-item').length;
        const accommodationList = document.getElementById('accommodation-list');
        const faqList = document.getElementById('faq-list');
        document.getElementById('add-accommodation')?.addEventListener('click', () => {
            const i = accommodationIndex++;
            const wrapper = document.createElement('div');
            wrapper.className = 'border rounded p-3 mb-3 accommodation-item';
            wrapper.dataset.spaceIndex = i;
            wrapper.innerHTML = '<div class="d-flex justify-content-between mb-2"><strong>Space <span class="space-number"></span></strong><div class="d-flex gap-1"><button type="button" class="btn btn-sm btn-light move-repeater-up">↑</button><button type="button" class="btn btn-sm btn-light move-repeater-down">↓</button><button type="button" class="btn btn-sm btn-outline-danger remove-repeater">Remove</button></div></div><div class="row g-2"><div class="col-md-5"><label class="form-label">Name *</label><input class="form-control" name="accommodations['+i+'][name]" required></div><div class="col-md-4"><label class="form-label">Category</label><input class="form-control" name="accommodations['+i+'][category]" placeholder="Bedroom, Bathroom, Parking"></div><div class="col-md-3"><label class="form-label">Area</label><input type="number" min="0" step=".01" class="form-control" name="accommodations['+i+'][area]"></div><div class="col-md-3"><label class="form-label">Unit</label><input class="form-control" name="accommodations['+i+'][area_unit]" value="sqft"></div><div class="col-md-9"><label class="form-label">Description</label><input class="form-control" name="accommodations['+i+'][description]"></div><div class="col-12"><label class="form-check"><input class="form-check-input" type="checkbox" value="1" checked name="accommodations['+i+'][is_included]"> <span class="form-check-label">Included with property</span></label></div><div class="col-12"><label class="form-label">Optional space image</label><input type="hidden" name="accommodations['+i+'][image_media_id]" id="media-accommodation-'+i+'" data-remove-flag-id="remove-accommodation-image-'+i+'"><input type="hidden" name="accommodations['+i+'][remove_image]" id="remove-accommodation-image-'+i+'" value="0"><div class="d-flex flex-wrap gap-2 mt-2"><button type="button" class="btn btn-sm btn-outline-primary" data-open-media-picker data-target="media-accommodation-'+i+'">Choose from Media Library</button><button type="button" class="btn btn-sm btn-outline-danger" data-clear-media data-target="media-accommodation-'+i+'">Remove selected image</button></div><div class="mt-2"><img class="rounded border" style="width:120px;height:80px;object-fit:cover" data-media-preview hidden alt="Space image preview"><span class="small text-muted" data-media-meta></span></div></div></div><div class="mt-3"><div class="d-flex justify-content-between align-items-center mb-2"><label class="form-label mb-0">Custom attributes</label><button type="button" class="btn btn-sm btn-outline-secondary add-space-attribute">Add attribute</button></div><div class="space-attributes"></div></div>';
            accommodationList.append(wrapper); updateNumbers();
        });
        document.getElementById('add-faq')?.addEventListener('click', () => {
            const i = faqIndex++;
            const wrapper = document.createElement('div');
            wrapper.className = 'border rounded p-3 mb-3 faq-item';
            wrapper.innerHTML = '<div class="d-flex justify-content-between mb-2"><strong>FAQ <span class="faq-number"></span></strong><div class="d-flex gap-1"><button type="button" class="btn btn-sm btn-light move-repeater-up">↑</button><button type="button" class="btn btn-sm btn-light move-repeater-down">↓</button><button type="button" class="btn btn-sm btn-outline-danger remove-repeater">Remove</button></div></div><label class="form-label">Question *</label><input class="form-control mb-2" name="faqs['+i+'][question]" required><label class="form-label">Answer *</label><textarea class="form-control" rows="3" name="faqs['+i+'][answer]" required></textarea>';
            faqList.append(wrapper); updateNumbers();
        });

        const propertyForm = document.getElementById('property-form');
        let preparingAccommodationVariants = false;
        propertyForm?.addEventListener('invalid', () => { preparingAccommodationVariants = false; }, true);
        propertyForm?.addEventListener('submit', async event => {
            if (preparingAccommodationVariants) return;
            const imageInputs = [...propertyForm.querySelectorAll('input[type="file"][name^="accommodations["][name$="[image]"]')].filter(input => input.files?.[0]);
            if (!imageInputs.length) return;
            event.preventDefault();
            preparingAccommodationVariants = true;
            try {
                for (const input of imageInputs) {
                    const match = input.name.match(/^accommodations\[(\d+)\]\[image\]$/);
                    if (!match) continue;
                    const file = input.files[0];
                    const bitmap = await createImageBitmap(file);
                    for (const [size, dimensions] of Object.entries({small:[320,240],medium:[900,700],large:[1600,1200]})) {
                        const ratio = Math.min(1, dimensions[0] / bitmap.width, dimensions[1] / bitmap.height);
                        const canvas = document.createElement('canvas');
                        canvas.width = Math.max(1, Math.round(bitmap.width * ratio));
                        canvas.height = Math.max(1, Math.round(bitmap.height * ratio));
                        canvas.getContext('2d').drawImage(bitmap, 0, 0, canvas.width, canvas.height);
                        const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/webp', .84));
                        if (!blob) throw new Error('Could not generate image variants.');
                        const variantInput = document.createElement('input');
                        variantInput.type = 'file';
                        variantInput.name = 'accommodation_variants[' + match[1] + '][' + size + ']';
                        const transfer = new DataTransfer();
                        transfer.items.add(new File([blob], size + '.webp', {type:'image/webp'}));
                        variantInput.files = transfer.files;
                        propertyForm.append(variantInput);
                    }
                    bitmap.close?.();
                }
                propertyForm.requestSubmit(event.submitter || undefined);
            } catch (error) {
                preparingAccommodationVariants = false;
                window.alert(error.message || 'Could not prepare accommodation images.');
            }
        });
        document.getElementById('add-property-feature')?.addEventListener('click', () => {
            const list = document.getElementById('property-feature-list');
            const row = document.createElement('div');
            row.className = 'input-group mb-2 property-feature-item';
            row.innerHTML = '<input class="form-control" name="features[]" maxlength="120" placeholder="Feature or amenity" required><button type="button" class="btn btn-outline-danger remove-property-feature">Remove</button>';
            list.append(row);
            row.querySelector('input')?.focus();
        });
        document.addEventListener('click', event => {
            const removeFeature = event.target.closest('.remove-property-feature');
            if (removeFeature) removeFeature.closest('.property-feature-item')?.remove();
        });
        document.addEventListener('click', event => {
            const addAttribute = event.target.closest('.add-space-attribute');
            if (addAttribute) {
                const item = addAttribute.closest('.accommodation-item');
                const index = item.dataset.spaceIndex;
                const attributes = item.querySelector('.space-attributes');
                const existingAttributeIndexes = [...attributes.querySelectorAll('input[name*="[attributes]["]')].map(input => Number(input.name.match(/\[attributes\]\[(\d+)\]/)?.[1] ?? -1));
                const attributeIndex = Math.max(-1, ...existingAttributeIndexes) + 1;
                const row = document.createElement('div');
                row.className = 'row g-2 mb-2 space-attribute';
                row.innerHTML = '<div class="col-5"><input class="form-control form-control-sm" name="accommodations['+index+'][attributes]['+attributeIndex+'][key]" placeholder="Attribute name" required></div><div class="col-5"><input class="form-control form-control-sm" name="accommodations['+index+'][attributes]['+attributeIndex+'][value]" placeholder="Value"></div><div class="col-2"><button type="button" class="btn btn-sm btn-outline-danger remove-space-attribute">×</button></div>';
                attributes.append(row);
                return;
            }
            const removeAttribute = event.target.closest('.remove-space-attribute');
            if (removeAttribute) { removeAttribute.closest('.space-attribute')?.remove(); return; }
            const moveUp = event.target.closest('.move-repeater-up');
            const moveDown = event.target.closest('.move-repeater-down');
            if (moveUp || moveDown) {
                const item = event.target.closest('.accommodation-item, .faq-item');
                if (moveUp && item.previousElementSibling) item.parentElement.insertBefore(item, item.previousElementSibling);
                if (moveDown && item.nextElementSibling) item.parentElement.insertBefore(item.nextElementSibling, item);
                updateNumbers(); return;
            }
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
