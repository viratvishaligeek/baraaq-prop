@extends('admin.layouts.app')
@section('title', $tag->exists ? 'Edit tag' : 'Create tag')
@section('content')
    <div class="mb-4"><h1 class="page-title">{{ $tag->exists ? 'Edit tag' : 'Create tag' }}</h1></div>
    <form method="POST" action="{{ $tag->exists ? route('admin.tags.update', $tag) : route('admin.tags.store') }}" class="content-card">
        @csrf @if($tag->exists) @method('PUT') @endif
        <label class="form-label" for="tag-name">Name</label><input class="form-control mb-3" id="tag-name" name="name" value="{{ old('name', $tag->name) }}" required maxlength="120">
        <label class="form-label" for="tag-slug">Slug (optional)</label><input class="form-control mb-3" id="tag-slug" name="slug" value="{{ old('slug', $tag->slug) }}" maxlength="140">
        <div class="d-flex gap-2"><button class="btn btn-primary">Save tag</button><a class="btn btn-light" href="{{ route('admin.tags.index') }}">Cancel</a></div>
    </form>
@endsection
