@extends('admin.layouts.app')
@section('title', 'Media library')
@section('content')
    <div class="mb-4">
        <h1 class="page-title">Media library</h1>
        <p class="text-muted">Upload and manage website files.</p>
    </div>
    <div class="content-card mb-4">
        <form method="POST" enctype="multipart/form-data" action="{{ route('admin.media.store') }}">@csrf<label
                class="form-label">Choose files (up to 20; 10 MB each)</label>
            <input class="form-control mb-3" type="file" id="media-upload-files" name="files[]" multiple required>
            <div id="media-upload-previews" class="d-flex flex-wrap gap-2 mb-3" aria-live="polite"></div>
            <button class="btn btn-primary">Upload files</button>
        </form>
    </div>
    <div class="row g-3">
        @forelse($media as $file)
            <div class="col-6 col-md-3 col-xl-2">
                <div class="content-card p-2 h-100">
                    @if (str_starts_with($file->mime_type ?? '', 'image/'))
                        <img class="w-100 rounded" style="height:120px;object-fit:cover"
                        src="{{ Storage::disk($file->disk)->url($file->path) }}" alt="{{ $file->alt_text }}">@else<div
                            class="p-4 text-center">
                            <i class="fa fa-file fa-2x">
                            </i>
                        </div>
                    @endif
                    <div class="small text-truncate mt-2" title="{{ $file->original_name }}">
                        {{ $file->original_name }}</div>
                    <form method="POST" action="{{ route('admin.media.update', $file) }}" class="mt-2">@csrf
                        @method('PATCH')<input class="form-control form-control-sm mb-2" name="alt_text"
                            value="{{ $file->alt_text }}" placeholder="Image alt text" maxlength="255">
                        <button class="btn btn-sm btn-outline-primary w-100">Save details</button>
                    </form>
                    <form method="POST" action="{{ route('admin.media.destroy', $file) }}"
                        onsubmit="return confirm('Delete this file?')">@csrf @method('DELETE')<button
                            class="btn btn-sm btn-outline-danger w-100 mt-2">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-muted">Your media library is empty.</p>
        @endforelse
    </div>
    {{ $media->links() }}
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('media-upload-files');
    const preview = document.getElementById('media-upload-previews');
    if (!input || !preview) return;
    let objectUrls = [];
    input.addEventListener('change', () => {
        objectUrls.forEach(url => URL.revokeObjectURL(url));
        objectUrls = [];
        preview.replaceChildren();
        [...input.files].filter(file => file.type.startsWith('image/')).forEach(file => {
            const url = URL.createObjectURL(file);
            objectUrls.push(url);
            const card = document.createElement('div');
            card.className = 'border rounded p-2';
            card.style.width = '140px';
            const image = document.createElement('img');
            image.src = url;
            image.alt = file.name;
            image.style.cssText = 'width:100%;height:88px;object-fit:cover;border-radius:4px';
            const label = document.createElement('div');
            label.className = 'small text-truncate mt-1';
            label.textContent = file.name;
            card.append(image, label);
            preview.append(card);
        });
    });
    window.addEventListener('pagehide', () => objectUrls.forEach(url => URL.revokeObjectURL(url)));
});
</script>
@endpush
