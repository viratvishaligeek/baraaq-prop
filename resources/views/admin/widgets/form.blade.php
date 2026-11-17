@extends('admin.layouts.app')
@section('title', $widget->exists ? 'Edit widget' : 'Create widget')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4"><div><h1 class="page-title">{{ $widget->exists ? 'Edit widget' : 'Create widget' }}</h1><p class="text-muted mb-0">A widget can be assigned to more than one area.</p></div><a class="btn btn-light" href="{{ route('admin.widgets.index') }}">Back</a></div>
@php($config = old('config', $widget->config ?? []))
<form method="POST" action="{{ $widget->exists ? route('admin.widgets.update',$widget) : route('admin.widgets.store') }}">
@csrf @if($widget->exists) @method('PUT') @endif
<div class="row g-4">
<div class="col-lg-8"><div class="content-card mb-4">
<div class="mb-3"><label class="form-label">Widget name</label><input class="form-control" name="name" value="{{ old('name',$widget->name) }}" required maxlength="120"></div>
<div class="row g-3"><div class="col-md-6"><label class="form-label">Widget type</label><select class="form-select" name="type" id="widget-type">@foreach(['text'=>'Text','image'=>'Image','html'=>'HTML','recent_posts'=>'Recent posts','categories'=>'Categories','featured_properties'=>'Featured properties','contact'=>'Contact information','social'=>'Social media','cta'=>'Call to action','custom'=>'Custom content'] as $value=>$label)<option value="{{ $value }}" @selected(old('type',$widget->type)===$value)>{{ $label }}</option>@endforeach</select></div><div class="col-md-6"><label class="form-label">Status</label><select class="form-select" name="status"><option value="published" @selected(old('status',$widget->status)==='published')>Enabled</option><option value="draft" @selected(old('status',$widget->status)==='draft')>Disabled</option></select></div></div>
<hr><div class="mb-3"><label class="form-label">Title</label><input class="form-control" name="title" value="{{ old('title',$config['title']??'') }}"></div>
<div class="mb-3"><label class="form-label">Text / body</label><textarea class="form-control" name="body" rows="5">{{ old('body',$config['body']??'') }}</textarea></div>
<div class="mb-3"><label class="form-label">HTML content (HTML widgets only)</label><textarea class="form-control font-monospace" name="html" rows="7">{{ old('html',$config['html']??'') }}</textarea></div>
<div class="row g-3"><div class="col-md-6"><label class="form-label">Image URL</label><input class="form-control" name="image_url" value="{{ old('image_url',$config['image_url']??'') }}"></div><div class="col-md-6"><label class="form-label">Link URL</label><input class="form-control" name="url" value="{{ old('url',$config['url']??'') }}"></div><div class="col-md-6"><label class="form-label">Button text</label><input class="form-control" name="button_text" value="{{ old('button_text',$config['button_text']??'') }}"></div></div>
<hr><h2 class="section-title">Contact details</h2><div class="row g-3"><div class="col-md-6"><label class="form-label">Phone</label><input class="form-control" name="phone" value="{{ old('phone',$config['phone']??'') }}"></div><div class="col-md-6"><label class="form-label">Email</label><input class="form-control" name="email" value="{{ old('email',$config['email']??'') }}"></div><div class="col-12"><label class="form-label">Address</label><textarea class="form-control" name="address">{{ old('address',$config['address']??'') }}</textarea></div></div>
</div></div>
<div class="col-lg-4"><div class="content-card"><h2 class="section-title">Display areas</h2><p class="small text-muted">Select every location where this widget should appear.</p>
@foreach($areas as $area)<label class="form-check mb-3"><input class="form-check-input" type="checkbox" name="area_ids[]" value="{{ $area->id }}" @checked(in_array($area->id,old('area_ids',$widget->exists?$widget->areas->pluck('id')->all():[])))><span class="form-check-label fw-semibold">{{ $area->name }} <small class="d-block text-muted">{{ $area->location }}</small></span></label>@endforeach
<button class="btn btn-primary w-100 mt-3">Save widget</button></div></div>
</div></form>
@endsection
