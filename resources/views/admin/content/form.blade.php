@extends('admin.layouts.app')
@section('title', ($item->exists ? 'Edit ' : 'Add ') . rtrim($type, 's'))
@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.content.index', $type) }}" class="text-decoration-none small">← {{ ucfirst($type) }}</a>
        <h1 class="page-title mt-2">{{ $item->exists ? 'Edit' : 'Add' }} {{ rtrim($type, 's') }}</h1>
        @if ($type === 'pages' && $item->exists)
            <a class="btn btn-outline-primary btn-sm mt-2" href="{{ route('admin.pages.builder', $item) }}">Open universal page builder</a>
        @elseif ($type === 'services' && $item->exists && (auth()->user()->isAdministrator() || auth()->user()->hasPermission('services.manage')))
            <a class="btn btn-outline-primary btn-sm mt-2" href="{{ route('admin.services.builder', $item) }}">Open service widget builder</a>
        @endif
    </div>
    <form method="POST" enctype="multipart/form-data"
        action="{{ $item->exists ? route('admin.content.update', [$type, $item->id]) : route('admin.content.store', $type) }}">
        @csrf
        @if ($item->exists)
            @method('PUT')
        @endif
        <div class="content-card">
            <div class="row g-3">
                @if ($type === 'testimonials')
                    <div class="col-md-6">
                        <label class="form-label">Customer name *</label>
                        <input class="form-control" name="name" value="{{ old('name', $item->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Designation</label>
                        <input class="form-control" name="designation" value="{{ old('designation', $item->designation) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Company</label>
                        <input class="form-control" name="company" value="{{ old('company', $item->company) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Rating (1–5)</label>
                        <input class="form-control" type="number" min="1" max="5" name="rating"
                            value="{{ old('rating', $item->rating) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Testimonial *</label>
                        <textarea class="form-control" rows="5" name="quote" required>{{ old('quote', $item->quote) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Photo</label>
                        <input class="form-control" type="file" name="photo" id="upload-testimonial-photo" accept="image/*">
                        <input type="hidden" name="photo_media_id" id="media-testimonial-photo"
                            value="{{ old('photo_media_id', $mediaItems->firstWhere('path', $item->photo)?->id) }}"
                            data-media-upload-input="upload-testimonial-photo">
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2"
                            data-open-media-picker data-target="media-testimonial-photo">Choose from Media Library</button>
                        <div class="mt-2">
                            <img class="rounded" width="88" data-media-preview
                                src="{{ $item->photo ? asset('storage/' . $item->photo) : '' }}"
                                alt="Testimonial photo preview" @if (!$item->photo) hidden @endif>
                            <span class="small text-muted" data-media-meta></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sort order</label>
                        <input class="form-control" type="number" min="0" name="sort_order"
                            value="{{ old('sort_order', $item->sort_order ?? 0) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Publication status</label>
                        @if (auth()->user()->isAdministrator() ||
                                auth()->user()->hasPermission($type . '.manage') ||
                                auth()->user()->hasPermission($type . '.publish'))
                            <select class="form-select" name="status">
                                <option value="draft" @selected(old('status', $item->status) === 'draft')>Draft</option>
                                <option value="published" @selected(old('status', $item->status ?? 'published') === 'published')>Published</option>
                        </select>@else<input type="hidden" name="status"
                                value="{{ old('status', $item->status ?? 'draft') }}">
                            <div class="form-text">Publish permission required.</div>
                        @endif
                    </div>
                    <div class="col-md-3 form-check mt-5">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                            id="testimonial-active" @checked(old('is_active', $item->is_active ?? true))>
                        <label class="form-check-label" for="testimonial-active">Published</label>
                    </div>
                @elseif($type === 'team')
                    <div class="col-md-6">
                        <label class="form-label">Name *</label>
                        <input class="form-control" name="name" value="{{ old('name', $item->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Designation</label>
                        <input class="form-control" name="designation"
                            value="{{ old('designation', $item->designation) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input class="form-control" name="email" value="{{ old('email', $item->email) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input class="form-control" name="phone" value="{{ old('phone', $item->phone) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Social links (JSON object)</label>
                        <textarea class="form-control" name="social_links" rows="3"
                            placeholder='{"linkedin":"https://linkedin.com/in/agent"}'>{{ old('social_links', json_encode($item->social_links ?? [], JSON_PRETTY_PRINT)) }}</textarea>
                        <small class="text-muted">Use network names as keys and full profile URLs as values.</small>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Bio</label>
                        <textarea class="form-control" name="bio" rows="5">{{ old('bio', $item->bio) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Photo</label>
                        <input class="form-control" type="file" name="photo" id="upload-team-photo" accept="image/*">
                        <input type="hidden" name="photo_media_id" id="media-team-photo"
                            value="{{ old('photo_media_id', $mediaItems->firstWhere('path', $item->photo)?->id) }}"
                            data-media-upload-input="upload-team-photo">
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2"
                            data-open-media-picker data-target="media-team-photo">Choose from Media Library</button>
                        <div class="mt-2"><img class="rounded" width="88" data-media-preview
                            src="{{ $item->photo ? asset('storage/' . $item->photo) : '' }}"
                            alt="Team photo preview" @if (!$item->photo) hidden @endif><span class="small text-muted" data-media-meta></span></div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sort order</label>
                        <input class="form-control" type="number" name="sort_order"
                            value="{{ old('sort_order', $item->sort_order ?? 0) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Publication status</label>
                        <select class="form-select" name="status">
                            <option value="draft" @selected(old('status', $item->status) === 'draft')>Draft</option>
                            <option value="published" @selected(old('status', $item->status ?? 'published') === 'published')>Published</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-check mt-5">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="active"
                            @checked(old('is_active', $item->is_active ?? true))>
                        <label class="form-check-label" for="active">Active</label>
                    </div>
                @elseif($type === 'categories')
                    <div class="col-md-6">
                        <label class="form-label">Name *</label>
                        <input class="form-control" name="name" value="{{ old('name', $item->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Type *</label>
                        <select class="form-select" name="type">
                            <option value="property" @selected(old('type', $item->type) === 'property')>Property</option>
                            <option value="post" @selected(old('type', $item->type) === 'post')>Blog</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description">{{ old('description', $item->description) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Category icon</label>
                        <input class="form-control" type="file" name="icon_path" id="upload-category-icon"
                            accept="image/png,image/jpeg,image/webp">
                        <input type="hidden" name="icon_media_id" id="media-category-icon"
                            value="{{ old('icon_media_id', $mediaItems->firstWhere('path', $item->icon_path)?->id) }}"
                            data-media-upload-input="upload-category-icon">
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2"
                            data-open-media-picker data-target="media-category-icon">Choose from Media Library</button>
                        <div class="mt-2"><img src="{{ $item->icon_path ? asset('storage/' . $item->icon_path) : '' }}"
                            alt="Category icon preview" class="rounded border" style="width:64px;height:64px;object-fit:contain"
                            data-media-preview @if (!$item->icon_path) hidden @endif><span class="small text-muted" data-media-meta></span></div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sort order</label>
                        <input class="form-control" type="number" min="0" name="sort_order"
                            value="{{ old('sort_order', $item->sort_order ?? 0) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-check-label">Active</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                id="category-active" @checked(old('is_active', $item->is_active ?? true))>
                            <label class="form-check-label" for="category-active">Show on website</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Parent category</label>
                        <select class="form-select" name="parent_id">
                            <option value="">No parent</option>
                            @foreach ($parentCategories as $parent)
                                <option class="parent-category-option" data-type="{{ $parent->type }}"
                                    value="{{ $parent->id }}" @selected(old('parent_id', $item->parent_id) == $parent->id)>{{ $parent->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @elseif($type === 'banners')
                    <div class="col-md-6">
                        <label class="form-label">Title *</label>
                        <input class="form-control" id="content-title" name="title" value="{{ old('title', $item->title) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Subtitle</label>
                        <input class="form-control" name="subtitle" value="{{ old('subtitle', $item->subtitle) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description">{{ old('description', $item->description) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Image</label>
                        <input class="form-control" type="file" name="image" id="upload-banner-image" accept="image/*">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Select banner image from Media Library</label>
                        <input type="hidden" name="image_media_id" id="media-banner-image"
                            value="{{ old('image_media_id', $mediaItems->firstWhere('path', $item->image)?->id) }}"
                            data-media-upload-input="upload-banner-image">
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2"
                            data-open-media-picker data-target="media-banner-image">Choose from Media Library</button>
                        <div class="mt-2"><img src="{{ $item->image ? asset('storage/' . $item->image) : '' }}"
                            alt="Banner image preview" class="rounded" style="max-width:180px;max-height:100px;object-fit:cover"
                            data-media-preview @if (!$item->image) hidden @endif><span class="small text-muted" data-media-meta></span></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Placement</label>
                        <input class="form-control" name="placement"
                            value="{{ old('placement', $item->placement ?? 'home') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Button text</label>
                        <input class="form-control" name="button_text"
                            value="{{ old('button_text', $item->button_text) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Button URL</label>
                        <input class="form-control" name="button_url"
                            value="{{ old('button_url', $item->button_url) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sort order</label>
                        <input class="form-control" type="number" min="0" name="sort_order"
                            value="{{ old('sort_order', $item->sort_order ?? 0) }}">
                    </div>
                    <div class="col-md-3 form-check mt-5">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                            id="banner-active" @checked(old('is_active', $item->is_active ?? true))>
                        <label class="form-check-label" for="banner-active">Active</label>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Starts at</label>
                        <input class="form-control" type="datetime-local" name="starts_at"
                            value="{{ old('starts_at', $item->starts_at?->format('Y-m-d\\TH:i')) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Ends at</label>
                        <input class="form-control" type="datetime-local" name="ends_at"
                            value="{{ old('ends_at', $item->ends_at?->format('Y-m-d\\TH:i')) }}">
                    </div>
                @else<div class="col-md-8">
                        <label class="form-label">Title *</label>
                        <input class="form-control" id="content-title" name="title" value="{{ old('title', $item->title) }}" required>
                    </div>
                    @if (in_array($type, ['posts', 'services']))
                        <div class="col-12">
                            <label class="form-label">Excerpt</label>
                            <textarea class="form-control" rows="2" name="excerpt">{{ old('excerpt', $item->excerpt) }}</textarea>
                        </div>
                    @endif
                    <div class="col-12">
                        <label class="form-label">Content</label>
                        <textarea class="form-control" rows="12" name="content" data-rich-editor>{{ old('content', $item->content) }}</textarea>
                    </div>
                    @if (in_array($type, ['posts', 'services']))
                        <div class="col-md-6">
                            <label class="form-label">Featured image</label>
                            <input class="form-control" type="file" name="featured_image" id="upload-featured-image" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Select featured image from Media Library</label>
                            <input type="hidden" name="featured_image_media_id" id="media-featured-image"
                                value="{{ old('featured_image_media_id', $mediaItems->firstWhere('path', $item->featured_image)?->id) }}"
                                data-media-upload-input="upload-featured-image">
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2"
                                data-open-media-picker data-target="media-featured-image">Choose from Media Library</button>
                            <div class="mt-2"><img src="{{ $item->featured_image ? asset('storage/' . $item->featured_image) : '' }}"
                                alt="Featured image preview" class="rounded" style="max-width:180px;max-height:100px;object-fit:cover"
                                data-media-preview @if (!$item->featured_image) hidden @endif><span class="small text-muted" data-media-meta></span></div>
                        </div>
                        @if ($type === 'posts')
                            <div class="col-12">
                                <label class="form-label">Categories</label>
                                <div class="row">
                                    @foreach ($categories as $category)
                                        <div class="col-md-4">
                                            <label class="form-check">
                                                <input class="form-check-input" type="checkbox" name="category_ids[]"
                                                    value="{{ $category->id }}" @checked(in_array($category->id, old('category_ids', $item->exists ? $item->categories->pluck('id')->all() : [])))>
                                                <span class="form-check-label">{{ $category->name }}</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif
                    <div class="col-md-6">
                        <label class="form-label">Slug</label>
                        <input class="form-control" id="content-slug" name="slug" value="{{ old('slug', $item->slug) }}"
                            placeholder="Generated from title when empty">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">SEO title</label>
                        <input class="form-control" id="content-meta-title" name="meta_title"
                            value="{{ old('meta_title', $item->meta_title) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">SEO description</label>
                        <textarea class="form-control" id="content-meta-description" name="meta_description">{{ old('meta_description', $item->meta_description) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Focus keyword</label>
                        <input class="form-control" name="focus_keyword" value="{{ old('focus_keyword', $item->focus_keyword) }}">
                    </div>
                    <div class="col-12">
                        <div class="border rounded p-3 bg-light" id="content-serp-preview">
                            <div class="small text-success" id="content-serp-url"></div>
                            <div class="fw-semibold text-primary" id="content-serp-title"></div>
                            <div class="small text-muted" id="content-serp-description"></div>
                            <div class="small text-muted mt-2"><span id="content-title-count">0</span> / 60 title characters · <span id="content-description-count">0</span> / 160 description characters</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Social sharing image</label>
                        <input class="form-control" type="file" name="og_image" id="upload-og-image" accept="image/*">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Select social image from Media Library</label>
                        <input type="hidden" name="og_image_media_id" id="media-og-image"
                            value="{{ old('og_image_media_id', $mediaItems->firstWhere('path', $item->og_image)?->id) }}"
                            data-media-upload-input="upload-og-image">
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2"
                            data-open-media-picker data-target="media-og-image">Choose from Media Library</button>
                        <div class="mt-2"><img src="{{ $item->og_image ? asset('storage/' . $item->og_image) : '' }}"
                            alt="Social sharing image preview" class="rounded" style="max-width:180px;max-height:100px;object-fit:cover"
                            data-media-preview @if (!$item->og_image) hidden @endif><span class="small text-muted" data-media-meta></span></div>
                    </div>
                @endif
                @if (in_array($type, ['posts', 'pages', 'services']))
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        @if (auth()->user()->isAdministrator() ||
                                auth()->user()->hasPermission($type . '.manage') ||
                                auth()->user()->hasPermission($type . '.publish'))
                            <select class="form-select" name="status">
                                <option value="draft" @selected(old('status', $item->status) === 'draft')>Draft</option>
                                <option value="published" @selected(old('status', $item->status) === 'published')>Published</option>
                                <option value="archived" @selected(old('status', $item->status) === 'archived')>Archived</option>
                        </select>@else<input type="hidden" name="status"
                                value="{{ old('status', $item->status ?? 'draft') }}">
                            <div class="form-text">Publish permission required.</div>
                        @endif
                    </div>
                @endif
            </div>
            <div class="mt-4">
                <button class="btn btn-primary">Save {{ rtrim($type, 's') }}</button>
            </div>
        </div>
    </form>
    @if ($type === 'categories')
        @push('scripts')
            <script>
                jQuery(function($) {
                    function filterParentCategories() {
                        const type = $('[name="type"]').val();
                        $('.parent-category-option').each(function() {
                            $(this).prop('disabled', $(this).data('type') !== type);
                            if ($(this).is(':selected') && $(this).data('type') !== type) $('[name="parent_id"]')
                                .val('');
                        });
                    }
                    $('[name="type"]').on('change', filterParentCategories);
                    filterParentCategories();
                });
            </script>
        @endpush
    @endif
    @if (in_array($type, ['posts', 'pages', 'services']))
        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js"></script>
            <script>

            document.addEventListener('DOMContentLoaded', function () {
                const title = document.getElementById('content-title');
                const slug = document.getElementById('content-slug');
                const seoTitle = document.getElementById('content-meta-title');
                const seoDescription = document.getElementById('content-meta-description');
                const previewUrl = document.getElementById('content-serp-url');
                const previewTitle = document.getElementById('content-serp-title');
                const previewDescription = document.getElementById('content-serp-description');
                const titleCount = document.getElementById('content-title-count');
                const descriptionCount = document.getElementById('content-description-count');
                if (!title || !slug) return;
                let slugManuallyEdited = Boolean(slug.value);
                const slugify = value => value.normalize('NFKD').replace(/[\\u0300-\\u036f]/g, '').toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
                slug.addEventListener('input', () => { slugManuallyEdited = true; updatePreview(); });
                title.addEventListener('input', () => { if (!slugManuallyEdited) slug.value = slugify(title.value); updatePreview(); });
                [seoTitle, seoDescription].forEach(input => input?.addEventListener('input', updatePreview));
                function updatePreview() {
                    const displayTitle = seoTitle?.value || title.value || 'Page title';
                    const displayDescription = seoDescription?.value || 'Add a meta description to preview your search result.';
                    if (previewTitle) previewTitle.textContent = displayTitle;
                    if (previewDescription) previewDescription.textContent = displayDescription;
                    if (previewUrl) previewUrl.textContent = window.location.origin + '/{{ $type === 'posts' ? 'blog' : $type }}/' + (slug.value || 'page-slug');
                    if (titleCount) titleCount.textContent = displayTitle.length;
                    if (descriptionCount) descriptionCount.textContent = displayDescription.length;
                }
                updatePreview();
            });

                document.addEventListener('DOMContentLoaded', function () {
                    if (!window.tinymce) return;
                    tinymce.init({
                        selector: 'textarea[data-rich-editor]',
                        license_key: 'gpl',
                        plugins: 'lists link image media table code searchreplace visualblocks wordcount anchor',
                        menubar: 'edit insert format table',
                        toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image media table | blockquote | code',
                        height: 420,
                        branding: false,
                        promotion: false,
                        file_picker_types: 'image',
                        file_picker_callback: (callback, value, meta) => {
                            if (meta.filetype !== 'image') return;
                            window.dispatchEvent(new CustomEvent('open-richtext-media-picker', { detail: { callback } }));
                        },
                        setup: editor => editor.on('change keyup undo redo', () => editor.save())
                    });
                    document.querySelector('form')?.addEventListener('submit', () => tinymce.triggerSave());
                });
            </script>
        @endpush
    @endif
@endsection
