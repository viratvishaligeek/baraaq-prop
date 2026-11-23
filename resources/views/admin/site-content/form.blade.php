@extends('admin.layouts.app')
@section('title',($item->exists?'Edit ':'Add ').ucfirst(str_replace('-',' ',$type)))
@section('content')
<div class="mb-4"><a href="{{ route('admin.site-content.index',$type) }}" class="small text-decoration-none">← Back</a><h1 class="page-title mt-2">{{ $item->exists?'Edit':'Add' }} {{ ucfirst(str_replace('-',' ',$type)) }}</h1></div>
<form method="POST" enctype="multipart/form-data" action="{{ $item->exists?route('admin.site-content.update',[$type,$item->id]):route('admin.site-content.store',$type) }}">@csrf @if($item->exists)@method('PUT')@endif
<div class="content-card"><div class="row g-3">
@if($type==='faqs')
<div class="col-12"><label class="form-label">Question *</label><input class="form-control" name="question" value="{{ old('question',$item->question) }}" required></div>
<div class="col-12"><label class="form-label">Answer *</label><textarea class="form-control" name="answer" rows="6" required>{{ old('answer',$item->answer) }}</textarea></div>
<div class="col-md-6"><label class="form-label">Category</label><input class="form-control" name="category" value="{{ old('category',$item->category) }}"></div><div class="col-md-3"><label class="form-label">Sort order</label><input class="form-control" type="number" min="0" name="sort_order" value="{{ old('sort_order',$item->sort_order??0) }}"></div><div class="col-md-3 form-check mt-5"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="active" @checked(old('is_active',$item->is_active??true))><label class="form-check-label" for="active">Active</label></div>
@else
<div class="col-md-8"><label class="form-label">Title *</label><input class="form-control" name="title" value="{{ old('title',$item->title) }}" required></div><div class="col-md-4"><label class="form-label">Slug</label><input class="form-control" name="slug" value="{{ old('slug',$item->slug) }}"></div>
@if($type==='neighborhood-guides')
<div class="col-md-4"><label class="form-label">Locality *</label><input class="form-control" name="locality" value="{{ old('locality',$item->locality) }}" required></div><div class="col-md-4"><label class="form-label">City</label><input class="form-control" name="city" value="{{ old('city',$item->city??'Bareilly') }}"></div><div class="col-md-4"><label class="form-label">State</label><input class="form-control" name="state" value="{{ old('state',$item->state??'Uttar Pradesh') }}"></div><div class="col-12"><label class="form-label">Excerpt</label><textarea class="form-control" name="excerpt" rows="2">{{ old('excerpt',$item->excerpt) }}</textarea></div>
@else<div class="col-12"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="3">{{ old('description',$item->description) }}</textarea></div>@endif
<div class="col-12"><label class="form-label">Page content</label><textarea class="form-control" name="content" rows="8">{{ old('content',$item->content) }}</textarea></div>
@php($currentImage = $item->image ?? $item->hero_image)
<div class="col-md-7">
    <label class="form-label">{{ $type === 'gallery' ? 'Gallery image' : 'Hero image' }}</label>
    <input type="hidden" name="image_media_id" id="media-site-content-image"
        value="{{ old('image_media_id', $mediaItems->firstWhere('path', $currentImage)?->id) }}"
        data-remove-flag-id="remove-site-content-image">
    <input type="hidden" name="remove_image" id="remove-site-content-image" value="0">
    <div class="d-flex flex-wrap gap-2 mt-2">
        <button type="button" class="btn btn-sm btn-outline-primary" data-open-media-picker data-target="media-site-content-image">Choose from Media Library</button>
        <button type="button" class="btn btn-sm btn-outline-danger" data-clear-media data-target="media-site-content-image">Remove selected image</button>
    </div>
    <div class="mt-2">
        <img src="{{ $currentImage ? Storage::disk('public')->url($currentImage) : '' }}"
            alt="{{ $item->alt_text ?? $item->title }}" style="max-width:220px;max-height:140px;object-fit:cover"
            data-media-preview @if (!$currentImage) hidden @endif>
        <span class="small text-muted" data-media-meta></span>
    </div>
    @if ($type === 'gallery' && !$item->exists)
        <div class="form-text">Select an image from the Media Library before saving this gallery item.</div>
    @endif
</div>
<div class="col-md-5"><label class="form-label">Image alt text</label><input class="form-control" name="alt_text" value="{{ old('alt_text',$item->alt_text) }}"></div>
<div class="col-md-4"><label class="form-label">Sort order</label><input class="form-control" type="number" min="0" name="sort_order" value="{{ old('sort_order',$item->sort_order??0) }}"></div><div class="col-md-4"><label class="form-label">Status</label><select class="form-select" name="status"><option value="draft" @selected(old('status',$item->status)==='draft')>Draft</option><option value="published" @selected(old('status',$item->status)==='published')>Published</option><option value="archived" @selected(old('status',$item->status)==='archived')>Archived</option></select></div><div class="col-md-4"><label class="form-label">Focus keyword</label><input class="form-control" name="focus_keyword" value="{{ old('focus_keyword',$item->focus_keyword) }}"></div>
<div class="col-md-6"><label class="form-label">Meta title</label><input class="form-control" name="meta_title" value="{{ old('meta_title',$item->meta_title) }}"></div><div class="col-md-6"><label class="form-label">Meta description</label><textarea class="form-control" name="meta_description" rows="2">{{ old('meta_description',$item->meta_description) }}</textarea></div><div class="col-md-6"><label class="form-label">Canonical URL</label><input class="form-control" type="url" name="canonical_url" value="{{ old('canonical_url',$item->canonical_url) }}" placeholder="Leave blank to use the page URL"></div><div class="col-md-6"><label class="form-label">Open Graph image path</label><input class="form-control" name="og_image" value="{{ old('og_image',$item->og_image) }}" placeholder="Storage path, if configured"></div>
@endif</div></div><div class="mt-3 d-flex gap-2"><button class="btn btn-primary">Save</button><a class="btn btn-light" href="{{ route('admin.site-content.index',$type) }}">Cancel</a></div></form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.querySelector('form input[type="file"][name="image"]');
    if (!input) return;
    let preview = document.querySelector('[data-local-content-preview]');
    if (!preview) {
        preview = document.createElement('img');
        preview.setAttribute('data-local-content-preview', '');
        preview.className = 'img-fluid rounded mt-2';
        preview.style.maxWidth = '220px';
        preview.style.maxHeight = '140px';
        preview.style.objectFit = 'cover';
        preview.hidden = true;
        input.insertAdjacentElement('afterend', preview);
    }
    input.addEventListener('change', function () {
        const file = input.files && input.files[0];
        if (!file) return;
        preview.src = URL.createObjectURL(file);
        preview.alt = file.name;
        preview.hidden = false;
    });
});
</script>
@endpush

@endsection