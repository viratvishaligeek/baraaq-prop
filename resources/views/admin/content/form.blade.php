@extends('admin.layouts.app')
@section('title', ($item->exists ? 'Edit ' : 'Add ') . rtrim($type, 's'))
@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.content.index', $type) }}" class="text-decoration-none small">← {{ ucfirst($type) }}</a>
        <h1 class="page-title mt-2">{{ $item->exists ? 'Edit' : 'Add' }} {{ rtrim($type, 's') }}</h1>
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
                        <input class="form-control" type="file" name="photo" accept="image/*">
                        <div class="col-md-6">
                            <label class="form-label">Select testimonial photo from Media Library</label>
                            <select class="form-select" name="photo_media_id">
                                <option value="">Keep uploaded file / no change</option>
                                @foreach ($mediaItems as $media)
                                    <option value="{{ $media->id }}" @selected($media->path === $item->photo)>
                                        {{ $media->original_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($item->photo)
                            <img class="mt-2 rounded" width="72" src="{{ asset('storage/' . $item->photo) }}"
                                alt="">
                        @endif
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
                        <input class="form-control" type="file" name="photo" accept="image/*">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Select photo from Media Library</label>
                        <select class="form-select" name="photo_media_id">
                            <option value="">Keep uploaded file / no change</option>
                            @foreach ($mediaItems as $media)
                                <option value="{{ $media->id }}" @selected($media->path === $item->photo)>
                                    {{ $media->original_name }}</option>
                            @endforeach
                        </select>
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
                        <input class="form-control" type="file" name="icon_path"
                            accept="image/png,image/jpeg,image/webp">
                        <div class="col-md-6">
                            <label class="form-label">Select icon from Media Library</label>
                            <select class="form-select" name="icon_media_id">
                                <option value="">Keep uploaded file / no change</option>
                                @foreach ($mediaItems as $media)
                                    <option value="{{ $media->id }}" @selected($media->path === $item->icon_path)>
                                        {{ $media->original_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($item->icon_path)
                            <img src="{{ asset('storage/' . $item->icon_path) }}" alt=""
                                class="mt-2 rounded border" style="width:64px;height:64px;object-fit:contain">
                        @endif
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
                        <input class="form-control" name="title" value="{{ old('title', $item->title) }}" required>
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
                        <input class="form-control" type="file" name="image" accept="image/*">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Select banner image from Media Library</label>
                        <select class="form-select" name="image_media_id">
                            <option value="">Keep uploaded file / no change</option>
                            @foreach ($mediaItems as $media)
                                <option value="{{ $media->id }}" @selected($media->path === $item->image)>
                                    {{ $media->original_name }}</option>
                            @endforeach
                        </select>
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
                        <input class="form-control" name="title" value="{{ old('title', $item->title) }}" required>
                    </div>
                    @if (in_array($type, ['posts', 'services']))
                        <div class="col-12">
                            <label class="form-label">Excerpt</label>
                            <textarea class="form-control" rows="2" name="excerpt">{{ old('excerpt', $item->excerpt) }}</textarea>
                        </div>
                    @endif
                    <div class="col-12">
                        <label class="form-label">Content</label>
                        <textarea class="form-control" rows="12" name="content">{{ old('content', $item->content) }}</textarea>
                    </div>
                    @if (in_array($type, ['posts', 'services']))
                        <div class="col-md-6">
                            <label class="form-label">Featured image</label>
                            <input class="form-control" type="file" name="featured_image" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Select featured image from Media Library</label>
                            <select class="form-select" name="featured_image_media_id">
                                <option value="">Keep uploaded file / no change</option>
                                @foreach ($mediaItems as $media)
                                    <option value="{{ $media->id }}" @selected($media->path === $item->featured_image)>
                                        {{ $media->original_name }}</option>
                                @endforeach
                            </select>
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
                        <input class="form-control" name="slug" value="{{ old('slug', $item->slug) }}"
                            placeholder="Generated from title when empty">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">SEO title</label>
                        <input class="form-control" name="meta_title"
                            value="{{ old('meta_title', $item->meta_title) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">SEO description</label>
                        <textarea class="form-control" name="meta_description">{{ old('meta_description', $item->meta_description) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Canonical URL</label>
                        <input class="form-control" type="url" name="canonical_url"
                            value="{{ old('canonical_url', $item->canonical_url) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Social sharing image</label>
                        <input class="form-control" type="file" name="og_image" accept="image/*">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Select social image from Media Library</label>
                        <select class="form-select" name="og_image_media_id">
                            <option value="">Keep uploaded file / no change</option>
                            @foreach ($mediaItems as $media)
                                <option value="{{ $media->id }}" @selected($media->path === $item->og_image)>
                                    {{ $media->original_name }}</option>
                            @endforeach
                        </select>
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
@endsection
