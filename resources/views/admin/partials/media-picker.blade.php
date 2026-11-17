<div class="modal fade" id="shared-media-picker" tabindex="-1" aria-labelledby="shared-media-picker-title" aria-hidden="true" data-endpoint="{{ route('admin.media.picker') }}">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="shared-media-picker-title">Choose image from Media Library</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="d-flex gap-2 mb-3" id="shared-media-search">
                    <input type="search" class="form-control" id="shared-media-query" placeholder="Search image filename" aria-label="Search image filename">
                    <button class="btn btn-outline-primary" type="submit">Search</button>
                </form>
                <div class="row g-3" id="shared-media-results" aria-live="polite"></div>
                <div class="text-center mt-3">
                    <button type="button" class="btn btn-outline-secondary" id="shared-media-more" hidden>Load more</button>
                </div>
                <p class="small text-muted mt-2 mb-0" id="shared-media-status" role="status">Search or select an image.</p>
            </div>
        </div>
    </div>
</div>
