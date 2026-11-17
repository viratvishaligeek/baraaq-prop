<div class="modal fade" id="shared-media-picker" tabindex="-1" aria-labelledby="shared-media-picker-title" aria-hidden="true" data-endpoint="{{ route('admin.media.picker') }}">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="shared-media-picker-title">Choose image from Media Library</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="d-flex flex-wrap align-items-center gap-2 mb-3" id="shared-media-upload" action="{{ route('admin.media.picker.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label class="form-label mb-0 small fw-semibold" for="shared-media-upload-files">Upload images</label>
                    <input class="form-control" style="max-width:420px" type="file" id="shared-media-upload-files" name="files[]" accept="image/jpeg,image/png,image/webp,image/gif" multiple required>
                    <button class="btn btn-primary" type="submit" id="shared-media-upload-submit">Upload to library</button>
                    <span class="small text-muted">JPG, PNG, WebP or GIF · 10 MB each · up to 20 per upload</span>
                </form>
                <form class="d-flex gap-2 mb-3" id="shared-media-search">
                    <input type="search" class="form-control" id="shared-media-query" placeholder="Search image filename" aria-label="Search image filename">
                    <button class="btn btn-outline-primary" type="submit">Search</button>
                </form>
                <div class="row g-3" id="shared-media-results" aria-live="polite"></div>
                <div class="d-flex flex-wrap justify-content-center gap-2 mt-3">
                    <button type="button" class="btn btn-outline-secondary" id="shared-media-more" hidden>Load more</button>
                    <button type="button" class="btn btn-primary" id="shared-media-apply-multiple" hidden>Use selected images</button>
                </div>
                <p class="small text-muted mt-2 mb-0" id="shared-media-status" role="status">Search or select an image.</p>
            </div>
        </div>
    </div>
</div>
