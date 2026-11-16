@extends('admin.layouts.app')
@section('title',$menu->exists?'Edit menu':'Create menu')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4"><div><h1 class="page-title">{{ $menu->exists?'Edit menu':'Create menu' }}</h1><p class="text-muted mb-0">Drag items to reorder. Select a parent to create nested navigation.</p></div><a href="{{ route('admin.menus.index') }}" class="btn btn-light">Back to menus</a></div>
<form method="POST" action="{{ $menu->exists?route('admin.menus.update',$menu):route('admin.menus.store') }}">@csrf @if($menu->exists)@method('PUT')@endif
<div class="content-card mb-4"><div class="row g-3"><div class="col-md-6"><label class="form-label">Menu name</label><input class="form-control" name="name" value="{{ old('name',$menu->name) }}" maxlength="120" required></div><div class="col-md-6"><label class="form-label">Location key</label><input class="form-control" name="location" value="{{ old('location',$menu->location) }}" placeholder="primary" maxlength="80" required></div></div></div>
<div class="content-card"><div class="d-flex justify-content-between align-items-center mb-3"><h2 class="section-title mb-0">Menu items</h2><button type="button" class="btn btn-sm btn-outline-primary" id="add-menu-item">Add item</button></div>
<div id="menu-items-list">
@foreach(old('items',$menuItems->map(fn($item)=>['label'=>$item->label,'url'=>$item->url,'target'=>$item->target,'parent_index'=>$item->parent_index,'is_active'=>$item->is_active?1:0])->all()) as $index=>$item)
<div class="menu-item-row border rounded p-3 mb-3" draggable="true"><div class="d-flex justify-content-between align-items-center mb-2"><span class="fw-semibold menu-item-handle" style="cursor:move">↕ Drag to reorder · Item <span class="item-number">{{ $index+1 }}</span></span><button type="button" class="btn btn-sm btn-outline-danger remove-menu-item">Remove</button></div><div class="row g-2"><div class="col-md-3"><label class="form-label">Label</label><input class="form-control" name="items[{{ $index }}][label]" value="{{ $item['label']??'' }}" required></div><div class="col-md-4"><label class="form-label">URL</label><input class="form-control" name="items[{{ $index }}][url]" value="{{ $item['url']??'' }}" placeholder="/properties or https://..."></div><div class="col-md-2"><label class="form-label">Parent</label><select class="form-select parent-select" name="items[{{ $index }}][parent_index]"><option value="">Top level</option>@foreach(old('items',$menuItems->map(fn($i)=>['label'=>$i->label])->all()) as $parentIndex=>$parent) @if($parentIndex<$index)<option value="{{ $parentIndex }}" @selected((string)($item['parent_index']??'')===(string)$parentIndex)>{{ $parent['label'] }}</option>@endif @endforeach</select></div><div class="col-md-2"><label class="form-label">Target</label><select class="form-select" name="items[{{ $index }}][target]"><option value="_self" @selected(($item['target']??'_self')==='_self')>Same tab</option><option value="_blank" @selected(($item['target']??'')==='_blank')>New tab</option></select></div><div class="col-md-1 d-flex align-items-end"><label class="form-check"><input class="form-check-input" type="checkbox" name="items[{{ $index }}][is_active]" value="1" @checked($item['is_active']??true)><span class="form-check-label">On</span></label></div></div></div>
@endforeach
</div><button class="btn btn-primary mt-3">Save menu</button></div></form>
@endsection
@push('scripts')
<script>
(function($){
let dragged=null;
function renumber(){
$('#menu-items-list .menu-item-row').each(function(index){
$(this).attr('data-index',index).find('.item-number').text(index+1);
$(this).find('[name]').each(function(){this.name=this.name.replace(/items\[\d+\]/,'items['+index+']');});
const select=$(this).find('.parent-select'),selected=select.val();
select.empty().append($('<option>',{value:'',text:'Top level'}));
$('#menu-items-list .menu-item-row').each(function(parentIndex){if(parentIndex<index)select.append($('<option>',{value:parentIndex,text:$(this).find('input[name$="[label]"]').val()||'Item '+(parentIndex+1)}));});
select.val(selected);
});
}
$('#add-menu-item').on('click',function(){
const i=$('#menu-items-list .menu-item-row').length;
const row='<div class="menu-item-row border rounded p-3 mb-3" draggable="true"><div class="d-flex justify-content-between mb-2"><span class="fw-semibold menu-item-handle" style="cursor:move">↕ Drag to reorder · Item <span class="item-number">'+(i+1)+'</span></span><button type="button" class="btn btn-sm btn-outline-danger remove-menu-item">Remove</button></div><div class="row g-2"><div class="col-md-3"><label class="form-label">Label</label><input class="form-control" name="items['+i+'][label]" required></div><div class="col-md-4"><label class="form-label">URL</label><input class="form-control" name="items['+i+'][url]" placeholder="/properties or https://..."></div><div class="col-md-2"><label class="form-label">Parent</label><select class="form-select parent-select" name="items['+i+'][parent_index]"><option value="">Top level</option></select></div><div class="col-md-2"><label class="form-label">Target</label><select class="form-select" name="items['+i+'][target]"><option value="_self">Same tab</option><option value="_blank">New tab</option></select></div><div class="col-md-1 d-flex align-items-end"><label class="form-check"><input class="form-check-input" type="checkbox" name="items['+i+'][is_active]" value="1" checked><span class="form-check-label">On</span></label></div></div></div>';
$('#menu-items-list').append(row);renumber();
});
$('#menu-items-list').on('click','.remove-menu-item',function(){$(this).closest('.menu-item-row').remove();renumber();});
$('#menu-items-list').on('input','input[name$="[label]"]',renumber);
$('#menu-items-list').on('dragstart','.menu-item-row',function(e){dragged=this;$(this).addClass('opacity-50');e.originalEvent.dataTransfer.effectAllowed='move';});
$('#menu-items-list').on('dragover','.menu-item-row',function(e){e.preventDefault();if(dragged&&dragged!==this)$(this).before(dragged);});
$('#menu-items-list').on('dragend','.menu-item-row',function(){$('.menu-item-row').removeClass('opacity-50');dragged=null;renumber();});
renumber();
})(jQuery);
</script>
@endpush