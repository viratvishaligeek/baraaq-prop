@extends('admin.layouts.app')
@section('title','SEO overview')
@section('content')
<div class="mb-4"><h1 class="page-title">SEO overview</h1><p class="text-muted">Find content missing page titles or meta descriptions. Edit an item to complete its SEO fields.</p></div>
<div class="content-card mb-4"><div class="d-flex justify-content-between align-items-center"><div><h2 class="section-title mb-1">Global SEO defaults</h2><p class="text-muted mb-0">Set the fallback description used by pages without their own metadata.</p></div><a class="btn btn-outline-primary" href="{{ route('admin.settings.edit') }}">Website settings</a></div></div>
@foreach([['Properties',$properties,'properties'],['Blog posts',$posts,'posts'],['Pages',$pages,'pages'],['Services',$services,'services']] as [$label,$items,$type])
<div class="content-card mb-4"><h2 class="section-title">{{ $label }}</h2><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Content</th><th>SEO title</th><th>Meta description</th><th></th></tr></thead><tbody>@forelse($items as $item)<tr><td>{{ $item->title }}</td><td>@if($item->meta_title)Set @else<span class="text-danger">Missing</span>@endif</td><td>@if($item->meta_description)Set @else<span class="text-danger">Missing</span>@endif</td><td class="text-end"><a class="btn btn-sm btn-light" href="{{ route('admin.content.edit',[$type,$item->id]) }}">Edit SEO</a></td></tr>@empty<tr><td colspan="4" class="text-muted text-center py-3">No content needs attention in this group.</td></tr>@endforelse</tbody></table></div></div>
@endforeach
@endsection