document.addEventListener('DOMContentLoaded',function(){if(window.toastr){toastr.options={closeButton:true,progressBar:true,positionClass:'toast-top-right',timeOut:3500};}});
document.addEventListener('DOMContentLoaded', function () {
    const picker = document.getElementById('shared-media-picker');
    if (!picker || !window.bootstrap?.Modal) return;

    const modal = bootstrap.Modal.getOrCreateInstance(picker);
    const endpoint = picker.dataset.endpoint;
    const results = document.getElementById('shared-media-results');
    const status = document.getElementById('shared-media-status');
    const more = document.getElementById('shared-media-more');
    const searchForm = document.getElementById('shared-media-search');
    const uploadForm = document.getElementById('shared-media-upload');
    const uploadButton = document.getElementById('shared-media-upload-submit');
    const query = document.getElementById('shared-media-query');
    const applyMultiple = document.getElementById('shared-media-apply-multiple');
    let targetInput = null;
    let nextPage = null;
    let multipleMode = false;
    const selectedItems = new Map();
    let richTextCallback = null;

    function updateTarget(item) {
        if (!targetInput) return;
        targetInput.value = String(item.id);
        const removeFlag = targetInput.dataset.removeFlagId ? document.getElementById(targetInput.dataset.removeFlagId) : null;
        if (removeFlag) { if (removeFlag.type === 'checkbox') removeFlag.checked = false; else removeFlag.value = '0'; }
        if (targetInput.dataset.mediaUploadInput) {
            const upload = document.getElementById(targetInput.dataset.mediaUploadInput);
            if (upload) {
                upload.value = '';
                if (upload.dataset.localObjectUrl) URL.revokeObjectURL(upload.dataset.localObjectUrl);
                delete upload.dataset.localObjectUrl;
            }
        }
        targetInput.dispatchEvent(new Event('input', { bubbles: true }));
        targetInput.dispatchEvent(new Event('change', { bubbles: true }));

        const preview = targetInput.dataset.mediaPreviewId
            ? document.getElementById(targetInput.dataset.mediaPreviewId)
            : targetInput.parentElement?.querySelector('[data-media-preview]');
        if (preview) {
            preview.src = item.url;
            preview.alt = item.alt || item.name;
            preview.hidden = false;
            preview.style.display = 'block';
        }

        const meta = targetInput.dataset.mediaMetaId
            ? document.getElementById(targetInput.dataset.mediaMetaId)
            : targetInput.parentElement?.querySelector('[data-media-meta]');
        if (meta) meta.textContent = item.name + ' · ' + (Number(item.size || 0) / 1024).toFixed(1) + ' KB';
        modal.hide();
    }

    async function loadMedia(url, append) {
        status.textContent = 'Loading images…';
        more.hidden = true;
        try {
            const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
            if (!response.ok) throw new Error('Media Library could not be loaded.');
            const payload = await response.json();
            if (!append) results.replaceChildren();

            (payload.data || []).forEach(item => {
                const column = document.createElement('div');
                column.className = 'col-6 col-sm-4 col-md-3 col-xl-2';
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'btn btn-light border w-100 h-100 p-2 text-start';
                const image = document.createElement('img');
                image.src = item.thumbnail_url || item.url;
                image.alt = item.alt || item.name;
                image.className = 'w-100 rounded mb-2';
                image.style.cssText = 'height:110px;object-fit:cover';
                const name = document.createElement('span');
                name.className = 'small d-block text-break';
                name.textContent = item.name;
                button.append(image, name);
                if (multipleMode && selectedItems.has(item.id)) {
                    button.classList.remove('btn-light');
                    button.classList.add('btn-primary');
                    button.setAttribute('aria-pressed', 'true');
                }
                button.addEventListener('click', () => {
                    if (richTextCallback) {
                        const callback = richTextCallback;
                        richTextCallback = null;
                        callback(item.url, { title: item.name, alt: item.alt || item.name });
                        modal.hide();
                        return;
                    }
                    if (!multipleMode) {
                        updateTarget(item);
                        return;
                    }
                    if (selectedItems.has(item.id)) {
                        selectedItems.delete(item.id);
                        button.classList.remove('btn-primary');
                        button.classList.add('btn-light');
                        button.setAttribute('aria-pressed', 'false');
                    } else {
                        selectedItems.set(item.id, item);
                        button.classList.remove('btn-light');
                        button.classList.add('btn-primary');
                        button.setAttribute('aria-pressed', 'true');
                    }
                    status.textContent = selectedItems.size + ' image(s) selected.';
                });
                column.append(button);
                results.append(column);
            });

            nextPage = payload.next_page_url || null;
            more.hidden = !nextPage;
            status.textContent = payload.data?.length
                ? (multipleMode ? 'Select one or more images, then choose Use selected images.' : 'Select an image to use it in this field.')
                : 'No images found. Upload images in Media Library first.';
        } catch (error) {
            status.textContent = error.message;
        }
    }

    window.addEventListener('open-richtext-media-picker', event => {
        if (typeof event.detail?.callback !== 'function') return;
        richTextCallback = event.detail.callback;
        multipleMode = false;
        selectedItems.clear();
        applyMultiple.hidden = true;
        query.value = '';
        modal.show();
        loadMedia(endpoint, false);
    });

    document.addEventListener('click', event => {
        const openButton = event.target.closest('[data-open-media-picker]');
        if (openButton) {
            targetInput = document.getElementById(openButton.dataset.target);
            if (!targetInput) return;
            multipleMode = openButton.hasAttribute('data-media-multiple');
            selectedItems.clear();
            if (multipleMode) {
                targetInput.querySelectorAll('input[type="hidden"]').forEach(input => {
                    const id = Number(input.value);
                    if (Number.isInteger(id) && id > 0) selectedItems.set(id, { id, existing: true });
                });
            }
            applyMultiple.hidden = !multipleMode;
            query.value = '';
            modal.show();
            loadMedia(endpoint, false);
            return;
        }

        const clearButton = event.target.closest('[data-clear-media]');
        if (clearButton) {
            const input = document.getElementById(clearButton.dataset.target);
            if (!input) return;
            input.value = '';
            const removeFlag = input.dataset.removeFlagId ? document.getElementById(input.dataset.removeFlagId) : null;
            if (removeFlag) { if (removeFlag.type === 'checkbox') removeFlag.checked = true; else removeFlag.value = '1'; }
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
            const preview = input.dataset.mediaPreviewId
                ? document.getElementById(input.dataset.mediaPreviewId)
                : input.parentElement?.querySelector('[data-media-preview]');
            if (preview) {
                preview.removeAttribute('src');
                preview.hidden = true;
                preview.style.display = 'none';
            }
            const meta = input.dataset.mediaMetaId
                ? document.getElementById(input.dataset.mediaMetaId)
                : input.parentElement?.querySelector('[data-media-meta]');
            if (meta) meta.textContent = '';
        }
    });

    applyMultiple?.addEventListener('click', () => {
        if (!multipleMode || !targetInput) return;

        const inputName = targetInput.dataset.inputName;
        if (!inputName) {
            status.textContent = 'This image collection is missing its input name.';
            return;
        }

        const existing = new Map();
        targetInput.querySelectorAll('input[type="hidden"]').forEach(input => {
            const id = Number(input.value);
            const wrapper = input.closest('.media-selection-item');
            if (Number.isInteger(id) && id > 0 && wrapper) existing.set(id, wrapper);
        });

        existing.forEach((wrapper, id) => {
            if (!selectedItems.has(id)) wrapper.remove();
        });

        selectedItems.forEach((item, id) => {
            if (existing.has(id)) return;
            const wrapper = document.createElement('div');
            wrapper.className = 'media-selection-item d-flex flex-wrap align-items-center gap-2 border rounded p-2 mt-2';

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = inputName;
            input.value = String(id);

            const image = document.createElement('img');
            image.src = item.url;
            image.alt = item.alt || item.name || 'Selected image';
            image.className = 'rounded border';
            image.style.cssText = 'width:76px;height:58px;object-fit:cover';

            const label = document.createElement('span');
            label.className = 'small text-break flex-grow-1';
            label.textContent = item.name || 'Selected Media Library image';

            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'btn btn-sm btn-outline-danger';
            remove.dataset.removeSelectedMedia = 'true';
            remove.textContent = 'Remove';

            wrapper.append(input, image, label, remove);
            targetInput.append(wrapper);
        });

        targetInput.dispatchEvent(new Event('change', { bubbles: true }));
        modal.hide();
    });

    document.addEventListener('click', event => {
        const removeSelected = event.target.closest('[data-remove-selected-media]');
        if (!removeSelected) return;
        removeSelected.closest('.media-selection-item')?.remove();
    });

    async function makeMediaVariant(file, maxWidth, maxHeight) {
        const bitmap = await createImageBitmap(file);
        const ratio = Math.min(1, maxWidth / bitmap.width, maxHeight / bitmap.height);
        const canvas = document.createElement('canvas');
        canvas.width = Math.max(1, Math.round(bitmap.width * ratio));
        canvas.height = Math.max(1, Math.round(bitmap.height * ratio));
        canvas.getContext('2d').drawImage(bitmap, 0, 0, canvas.width, canvas.height);
        bitmap.close?.();
        const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/webp', .84));
        if (!blob) throw new Error('Could not generate a responsive image variant.');
        return new File([blob], file.name.replace(/\.[^.]+$/, '') + '.webp', { type: 'image/webp' });
    }

    uploadForm?.addEventListener('submit', async event => {
        event.preventDefault();
        const files = uploadForm.querySelector('input[type="file"]');
        if (!files?.files?.length) return;
        const originalLabel = uploadButton?.textContent;
        if (uploadButton) {
            uploadButton.disabled = true;
            uploadButton.textContent = 'Uploading…';
        }
        status.textContent = 'Uploading images to the Media Library…';
        try {
            const formData = new FormData(uploadForm);
            for (const [index, file] of Array.from(files.files).entries()) {
                if (!file.type.startsWith('image/') || file.type === 'image/gif') continue;
                for (const [size, dimensions] of Object.entries({ small: [320, 240], medium: [900, 700], large: [1600, 1200] })) {
                    formData.append('variants[' + index + '][' + size + ']', await makeMediaVariant(file, ...dimensions));
                }
            }
            const response = await fetch(uploadForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });
            const payload = await response.json().catch(() => ({}));
            if (!response.ok) {
                const errors = Object.values(payload.errors || {}).flat();
                throw new Error(errors[0] || payload.message || 'Image upload failed.');
            }
            files.value = '';
            const url = new URL(endpoint, window.location.origin);
            if (query.value.trim()) url.searchParams.set('search', query.value.trim());
            await loadMedia(url.toString(), false);
            status.textContent = (payload.data?.length || 0) + ' image(s) uploaded. Select an image below.';
        } catch (error) {
            status.textContent = error.message;
        } finally {
            if (uploadButton) {
                uploadButton.disabled = false;
                uploadButton.textContent = originalLabel || 'Upload to library';
            }
        }
    });

    searchForm?.addEventListener('submit', event => {
        event.preventDefault();
        const url = new URL(endpoint, window.location.origin);
        if (query.value.trim()) url.searchParams.set('search', query.value.trim());
        loadMedia(url.toString(), false);
    });

    // Every Admin image upload gets an immediate local preview, while the
    // matching Media Library ID is cleared so the newly uploaded file wins.
    document.addEventListener('change', event => {
        const fileInput = event.target.closest('input[type="file"][accept*="image"]');
        if (!fileInput) return;

        const files = Array.from(fileInput.files || []).filter(file => file.type.startsWith('image/'));
        const mediaField = Array.from(document.querySelectorAll('[data-media-upload-input]'))
            .find(input => input.dataset.mediaUploadInput === fileInput.id);

        if (mediaField) {
            mediaField.value = '';
            mediaField.dispatchEvent(new Event('change', { bubbles: true }));
        }

        const containerId = fileInput.dataset.previewContainerId;
        if (containerId) {
            const container = document.getElementById(containerId);
            if (!container) return;
            container.replaceChildren();
            files.forEach(file => {
                const image = document.createElement('img');
                image.src = URL.createObjectURL(file);
                image.alt = file.name;
                image.className = 'rounded border';
                image.style.cssText = 'width:88px;height:68px;object-fit:cover';
                container.append(image);
            });
            return;
        }

        const preview = mediaField?.parentElement?.querySelector('[data-media-preview]')
            || fileInput.parentElement?.querySelector('[data-media-preview]');
        if (!preview) return;

        if (fileInput.dataset.localObjectUrl) URL.revokeObjectURL(fileInput.dataset.localObjectUrl);
        if (!files.length) {
            if (fileInput.dataset.localObjectUrl) URL.revokeObjectURL(fileInput.dataset.localObjectUrl);
            delete fileInput.dataset.localObjectUrl;
            preview.removeAttribute('src');
            preview.hidden = true;
            return;
        }

        fileInput.dataset.localObjectUrl = URL.createObjectURL(files[0]);
        preview.src = fileInput.dataset.localObjectUrl;
        preview.alt = files[0].name;
        preview.hidden = false;
        preview.style.display = 'block';
    });

    applyMultiple?.addEventListener('click', () => {
        if (!multipleMode || !targetInput) return;
        const existingIds = new Set([...targetInput.querySelectorAll('input[type="hidden"]')].map(input => input.value));
        selectedItems.forEach(item => {
            if (existingIds.has(String(item.id))) return;
            const selection = document.createElement('div');
            selection.className = 'd-inline-flex align-items-start gap-1 border rounded p-1';
            selection.dataset.mediaSelection = String(item.id);
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = targetInput.dataset.inputName || 'gallery_media_ids[]';
            hidden.value = String(item.id);
            const image = document.createElement('img');
            image.src = item.url;
            image.alt = item.alt || item.name;
            image.className = 'rounded';
            image.style.cssText = 'width:76px;height:58px;object-fit:cover';
            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'btn btn-sm btn-outline-danger py-0 px-2';
            remove.textContent = '×';
            remove.setAttribute('aria-label', 'Remove ' + (item.alt || item.name));
            remove.addEventListener('click', () => selection.remove());
            selection.append(hidden, image, remove);
            targetInput.append(selection);
        });
        modal.hide();
    });

    more?.addEventListener('click', () => {
        if (nextPage) loadMedia(nextPage, true);
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const previewUrls = new WeakMap();
    document.querySelectorAll('input[type="file"][accept*="image"]:not(#media-upload-files)').forEach(input => {
        let preview = input.parentElement?.querySelector('[data-local-upload-preview]');
        if (!preview) {
            preview = document.createElement('img');
            preview.dataset.localUploadPreview = 'true';
            preview.alt = 'Selected upload preview';
            preview.hidden = true;
            preview.className = 'd-block rounded border mt-2';
            preview.style.cssText = 'max-width:180px;max-height:120px;object-fit:contain';
            input.insertAdjacentElement('afterend', preview);
        }

        input.addEventListener('change', () => {
            const previousUrl = previewUrls.get(input);
            if (previousUrl) URL.revokeObjectURL(previousUrl);
            const file = input.files?.[0];
            if (!file || !file.type.startsWith('image/')) {
                preview.removeAttribute('src');
                preview.hidden = true;
                return;
            }
            const url = URL.createObjectURL(file);
            previewUrls.set(input, url);
            preview.src = url;
            preview.hidden = false;
        });
    });

    window.addEventListener('pagehide', () => {
        document.querySelectorAll('input[type="file"][accept*="image"]').forEach(input => {
            const url = previewUrls.get(input);
            if (url) URL.revokeObjectURL(url);
        });
    });
});
