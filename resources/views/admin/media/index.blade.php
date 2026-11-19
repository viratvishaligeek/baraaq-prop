@extends('admin.layouts.app')
@section('title', 'Media library')
@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div><h1 class="page-title mb-1">Media library</h1><p class="text-muted mb-0">Upload, organize, crop and resize your website media.</p></div>
        <span class="badge bg-light text-dark">{{ $media->total() }} files</span>
    </div>

    <div class="content-card mb-4">
        <form method="POST" enctype="multipart/form-data" action="{{ route('admin.media.store') }}" id="media-upload-form">
            @csrf
            <label class="form-label" for="media-upload-files">Choose files (up to 20; 10 MB each)</label>
            <input class="form-control mb-3" type="file" id="media-upload-files" name="files[]" multiple required accept="image/jpeg,image/png,image/webp,image/gif,application/pdf,video/mp4">
            <div id="media-upload-previews" class="d-flex flex-wrap gap-2 mb-3" aria-live="polite"></div>
            <div class="small text-muted mb-3">For supported images, the browser creates small, medium and large responsive variants during upload. Original files are preserved.</div>
            <button class="btn btn-primary" id="media-upload-submit">Upload files</button>
            <span class="small text-muted ms-2" id="media-upload-status" role="status"></span>
        </form>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <form id="bulk-media-form" method="POST" action="{{ route('admin.media.bulk-destroy') }}" class="content-card mb-3 d-flex flex-wrap align-items-center justify-content-between gap-2" onsubmit="return confirm('Delete all selected media files? Files used by website content will be retained.')">
        @csrf @method('DELETE')
        <div class="d-flex align-items-center gap-3">
            <label class="form-check mb-0"><input class="form-check-input" type="checkbox" id="select-all-media"> <span class="form-check-label">Select all on this page</span></label>
            <span class="small text-muted" id="media-selection-count">0 selected</span>
        </div>
        <div class="d-flex gap-2"><button class="btn btn-light btn-sm" type="button" id="cancel-media-selection">Clear selection</button><button class="btn btn-outline-danger btn-sm" type="submit" id="bulk-delete-media" disabled>Delete selected</button></div>
    </form>

    <div class="row g-3">
        @forelse($media as $file)
            <div class="col-6 col-md-4 col-xl-3 col-xxl-2">
                <article class="content-card p-2 h-100">
                    <label class="form-check mb-2"><input class="form-check-input media-select" type="checkbox" name="media_ids[]" value="{{ $file->id }}" form="bulk-media-form"> <span class="small text-muted">Select file</span></label>
                    @if (str_starts_with($file->mime_type ?? '', 'image/'))
                        <img class="w-100 rounded media-preview-image" style="height:150px;object-fit:cover" src="{{ $file->thumbnail_url }}" alt="{{ $file->alt_text ?: $file->original_name }}" loading="lazy">
                        <button type="button" class="btn btn-sm btn-outline-primary w-100 mt-2" data-edit-media data-source="{{ $file->url }}" data-save-url="{{ route('admin.media.edited-copy', $file) }}" data-alt="{{ $file->alt_text }}">Crop / resize</button>
                        <div class="small text-muted mt-2">Original · {{ number_format($file->size / 1024, 1) }} KB</div>
                        @if ($file->small_path || $file->medium_path || $file->large_path)
                            <div class="small text-success">Responsive variants ready</div>
                        @endif
                    @else
                        <div class="p-4 text-center bg-light rounded"><i class="fa fa-file fa-2x"></i><div class="small mt-2">{{ strtoupper(pathinfo($file->original_name, PATHINFO_EXTENSION)) }}</div></div>
                        <div class="small text-muted mt-2">{{ number_format($file->size / 1024, 1) }} KB</div>
                    @endif
                    <div class="small text-truncate mt-2 fw-semibold" title="{{ $file->original_name }}">{{ $file->original_name }}</div>
                    <form method="POST" action="{{ route('admin.media.update', $file) }}" class="mt-2">
                        @csrf @method('PATCH')
                        <label class="form-label small mb-1" for="media-alt-{{ $file->id }}">Alt text</label>
                        <input id="media-alt-{{ $file->id }}" class="form-control form-control-sm mb-2" name="alt_text" value="{{ $file->alt_text }}" placeholder="Describe the image" maxlength="255">
                        <button class="btn btn-sm btn-outline-secondary w-100">Save details</button>
                    </form>
                    <form method="POST" action="{{ route('admin.media.destroy', $file) }}" onsubmit="return confirm('Delete this file? Referenced media will be retained.')">
                        @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger w-100 mt-2">Delete</button>
                    </form>
                </article>
            </div>
        @empty
            <div class="col-12"><div class="content-card text-center py-5"><i class="fa-regular fa-images fa-2x text-muted mb-3"></i><h2 class="h5">Your media library is empty</h2><p class="text-muted mb-0">Upload images or documents to use them across the website.</p></div></div>
        @endforelse
    </div>
    <div class="mt-4">{{ $media->links() }}</div>

    <dialog id="media-editor-dialog" class="border-0 rounded-3 shadow" style="width:min(900px,96vw);max-height:94vh;padding:0">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center"><div><h2 class="h5 mb-0">Image editor</h2><p class="small text-muted mb-0">Drag on the image to select a crop area. Save creates a new copy; the original stays unchanged.</p></div><button type="button" class="btn-close" id="media-editor-close" aria-label="Close"></button></div>
        <div class="p-3">
            <div class="bg-light rounded p-2 text-center"><canvas id="media-editor-canvas" style="max-width:100%;max-height:55vh;touch-action:none;cursor:crosshair"></canvas></div>
            <div class="row g-3 mt-1">
                <div class="col-6 col-md-3"><label class="form-label small" for="media-output-width">Output width (px)</label><input class="form-control" type="number" id="media-output-width" min="1" max="8000"></div>
                <div class="col-6 col-md-3"><label class="form-label small" for="media-output-height">Output height (px)</label><input class="form-control" type="number" id="media-output-height" min="1" max="8000"></div>
                <div class="col-12 col-md-6"><label class="form-label small" for="media-edited-alt">Alt text</label><input class="form-control" id="media-edited-alt" maxlength="255"></div>
            </div>
            <div class="d-flex flex-wrap justify-content-between gap-2 mt-3"><span class="small text-muted" id="media-crop-status">Drag to select the crop area.</span><div class="d-flex gap-2"><button type="button" class="btn btn-light" id="media-editor-reset">Reset crop</button><button type="button" class="btn btn-primary" id="media-editor-save">Save edited copy</button></div></div>
        </div>
    </dialog>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const uploadForm = document.getElementById('media-upload-form');
    const input = document.getElementById('media-upload-files');
    const preview = document.getElementById('media-upload-previews');
    const uploadButton = document.getElementById('media-upload-submit');
    const uploadStatus = document.getElementById('media-upload-status');
    const selectAll = document.getElementById('select-all-media');
    const selectionCount = document.getElementById('media-selection-count');
    const deleteButton = document.getElementById('bulk-delete-media');
    const updateSelection = () => {
        const boxes = [...document.querySelectorAll('.media-select')];
        const selected = boxes.filter(box => box.checked).length;
        selectionCount.textContent = selected + ' selected';
        deleteButton.disabled = selected === 0;
        selectAll.checked = boxes.length > 0 && selected === boxes.length;
        selectAll.indeterminate = selected > 0 && selected < boxes.length;
    };
    selectAll?.addEventListener('change', () => { document.querySelectorAll('.media-select').forEach(box => box.checked = selectAll.checked); updateSelection(); });
    document.querySelectorAll('.media-select').forEach(box => box.addEventListener('change', updateSelection));
    document.getElementById('cancel-media-selection')?.addEventListener('click', () => { document.querySelectorAll('.media-select').forEach(box => box.checked = false); updateSelection(); });
    updateSelection();

    input?.addEventListener('change', () => {
        preview.replaceChildren();
        [...input.files].forEach(file => {
            if (!file.type.startsWith('image/')) return;
            const url = URL.createObjectURL(file);
            const card = document.createElement('div'); card.className = 'border rounded p-2'; card.style.width = '140px';
            const image = document.createElement('img'); image.src = url; image.alt = file.name; image.style.cssText = 'width:100%;height:88px;object-fit:cover;border-radius:4px';
            const label = document.createElement('div'); label.className = 'small text-truncate mt-1'; label.textContent = file.name;
            card.append(image, label); preview.append(card);
        });
    });

    const makeVariant = async (file, maxWidth, maxHeight) => {
        const bitmap = await createImageBitmap(file);
        const ratio = Math.min(1, maxWidth / bitmap.width, maxHeight / bitmap.height);
        const canvas = document.createElement('canvas');
        canvas.width = Math.max(1, Math.round(bitmap.width * ratio));
        canvas.height = Math.max(1, Math.round(bitmap.height * ratio));
        canvas.getContext('2d').drawImage(bitmap, 0, 0, canvas.width, canvas.height);
        bitmap.close?.();
        const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/webp', .84));
        if (!blob) throw new Error('Could not generate an image variant.');
        return new File([blob], file.name.replace(/\.[^.]+$/, '') + '.webp', {type: 'image/webp'});
    };
    uploadForm?.addEventListener('submit', async event => {
        event.preventDefault();
        if (!input.files.length) return;
        uploadButton.disabled = true; uploadStatus.textContent = 'Preparing responsive image variants…';
        try {
            const formData = new FormData(uploadForm);
            for (const [index, file] of [...input.files].entries()) {
                if (!file.type.startsWith('image/') || file.type === 'image/gif') continue;
                for (const [size, dimensions] of Object.entries({small:[320,240], medium:[900,700], large:[1600,1200]})) {
                    formData.append('variants['+index+']['+size+']', await makeVariant(file, ...dimensions));
                }
            }
            uploadStatus.textContent = 'Uploading originals and variants…';
            const response = await fetch(uploadForm.action, {method:'POST', body:formData, headers:{'Accept':'text/html','X-Requested-With':'XMLHttpRequest'}});
            if (!response.ok) throw new Error('Upload failed. Check file types, sizes and validation messages.');
            window.location.assign(response.url || window.location.href);
        } catch (error) {
            uploadStatus.textContent = error.message || 'Upload failed.';
            uploadButton.disabled = false;
        }
    });

    const dialog = document.getElementById('media-editor-dialog');
    const canvas = document.getElementById('media-editor-canvas');
    const context = canvas.getContext('2d');
    const widthInput = document.getElementById('media-output-width');
    const heightInput = document.getElementById('media-output-height');
    const cropStatus = document.getElementById('media-crop-status');
    let sourceImage = null, saveUrl = null, crop = null, dragStart = null;
    const drawEditor = () => {
        if (!sourceImage) return;
        context.clearRect(0,0,canvas.width,canvas.height);
        context.drawImage(sourceImage,0,0);
        if (crop) {
            context.save(); context.fillStyle='rgba(0,0,0,.35)';
            context.fillRect(0,0,canvas.width,canvas.height);
            context.clearRect(crop.x,crop.y,crop.w,crop.h);
            context.drawImage(sourceImage,crop.x,crop.y,crop.w,crop.h,crop.x,crop.y,crop.w,crop.h);
            context.strokeStyle='#fff'; context.lineWidth=Math.max(2,canvas.width/500); context.strokeRect(crop.x,crop.y,crop.w,crop.h);
            context.restore();
        }
    };
    document.querySelectorAll('[data-edit-media]').forEach(button => button.addEventListener('click', () => {
        saveUrl = button.dataset.saveUrl;
        sourceImage = new Image();
        sourceImage.onload = () => {
            canvas.width = sourceImage.naturalWidth; canvas.height = sourceImage.naturalHeight;
            crop = {x:0,y:0,w:canvas.width,h:canvas.height};
            widthInput.value = canvas.width; heightInput.value = canvas.height;
            document.getElementById('media-edited-alt').value = button.dataset.alt || '';
            cropStatus.textContent = canvas.width+' × '+canvas.height+' px · Drag to crop';
            drawEditor(); dialog.showModal();
        };
        sourceImage.onerror = () => { cropStatus.textContent = 'Unable to load this image.'; };
        sourceImage.src = button.dataset.source;
    }));
    const point = event => { const rect=canvas.getBoundingClientRect(); return {x:Math.max(0,Math.min(canvas.width,(event.clientX-rect.left)*canvas.width/rect.width)),y:Math.max(0,Math.min(canvas.height,(event.clientY-rect.top)*canvas.height/rect.height))}; };
    canvas.addEventListener('pointerdown', event => { if(!sourceImage)return; canvas.setPointerCapture(event.pointerId); dragStart=point(event); crop={x:dragStart.x,y:dragStart.y,w:1,h:1}; drawEditor(); });
    canvas.addEventListener('pointermove', event => { if(!dragStart)return; const p=point(event); crop={x:Math.min(p.x,dragStart.x),y:Math.min(p.y,dragStart.y),w:Math.abs(p.x-dragStart.x),h:Math.abs(p.y-dragStart.y)}; drawEditor(); });
    const finishCrop = () => { if(!dragStart)return; dragStart=null; if(crop.w<2||crop.h<2)crop={x:0,y:0,w:canvas.width,h:canvas.height}; widthInput.value=Math.round(crop.w); heightInput.value=Math.round(crop.h); cropStatus.textContent=Math.round(crop.w)+' × '+Math.round(crop.h)+' px crop selected'; drawEditor(); };
    canvas.addEventListener('pointerup',finishCrop); canvas.addEventListener('pointercancel',finishCrop);
    document.getElementById('media-editor-reset').addEventListener('click',()=>{crop={x:0,y:0,w:canvas.width,h:canvas.height};widthInput.value=canvas.width;heightInput.value=canvas.height;drawEditor();});
    document.getElementById('media-editor-close').addEventListener('click',()=>dialog.close());
    dialog.addEventListener('click',event=>{if(event.target===dialog)dialog.close();});
    document.getElementById('media-editor-save').addEventListener('click',async event=>{
        if(!sourceImage||!crop||!saveUrl)return;
        const outWidth=Number(widthInput.value),outHeight=Number(heightInput.value);
        if(!Number.isInteger(outWidth)||!Number.isInteger(outHeight)||outWidth<1||outHeight<1||outWidth>8000||outHeight>8000){cropStatus.textContent='Enter valid output dimensions (1–8000 px).';return;}
        const output=document.createElement('canvas');output.width=outWidth;output.height=outHeight;
        output.getContext('2d').drawImage(sourceImage,crop.x,crop.y,crop.w,crop.h,0,0,outWidth,outHeight);
        cropStatus.textContent='Saving edited image…';
        const blob=await new Promise(resolve=>output.toBlob(resolve,'image/webp',.9));
        if(!blob){cropStatus.textContent='Could not encode the edited image.';return;}
        const formData=new FormData();formData.append('image',new File([blob],'edited.webp',{type:'image/webp'}));formData.append('alt_text',document.getElementById('media-edited-alt').value);formData.append('_token',document.querySelector('meta[name="csrf-token"]').content);
        try{
            for (const [size, dimensions] of Object.entries({small:[320,240],medium:[900,700],large:[1600,1200]})) {
                const ratio=Math.min(1,dimensions[0]/outWidth,dimensions[1]/outHeight);
                const variantCanvas=document.createElement('canvas');variantCanvas.width=Math.max(1,Math.round(outWidth*ratio));variantCanvas.height=Math.max(1,Math.round(outHeight*ratio));
                variantCanvas.getContext('2d').drawImage(output,0,0,variantCanvas.width,variantCanvas.height);
                const variantBlob=await new Promise(resolve=>variantCanvas.toBlob(resolve,'image/webp',.84));
                if(!variantBlob)throw new Error('Could not generate responsive image variants.');
                formData.append('variants['+size+']',new File([variantBlob],size+'.webp',{type:'image/webp'}));
            }
            const response=await fetch(saveUrl,{method:'POST',body:formData,headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});
            if(!response.ok){const payload=await response.json().catch(()=>({}));throw new Error(payload.message||'Could not save the edited image.');}
            window.location.reload();
        }catch(error){cropStatus.textContent=error.message;}
    });
});
</script>
@endpush
