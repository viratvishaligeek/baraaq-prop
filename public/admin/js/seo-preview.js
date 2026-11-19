document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-seo-preview]').forEach(preview => {
        const titleInput = document.querySelector(preview.dataset.titleInput);
        const slugInput = document.querySelector(preview.dataset.slugInput);
        const metaTitleInput = document.querySelector(preview.dataset.metaTitleInput);
        const metaDescriptionInput = document.querySelector(preview.dataset.metaDescriptionInput);
        const urlOutput = preview.querySelector(preview.dataset.urlOutput) || document.querySelector(preview.dataset.urlOutput);
        const titleOutput = preview.querySelector(preview.dataset.titleOutput) || document.querySelector(preview.dataset.titleOutput);
        const descriptionOutput = preview.querySelector(preview.dataset.descriptionOutput) || document.querySelector(preview.dataset.descriptionOutput);
        const titleCount = preview.querySelector(preview.dataset.titleCount) || document.querySelector(preview.dataset.titleCount);
        const descriptionCount = preview.querySelector(preview.dataset.descriptionCount) || document.querySelector(preview.dataset.descriptionCount);
        const refresh = () => {
            const title = metaTitleInput?.value || titleInput?.value || 'Page title';
            const description = metaDescriptionInput?.value || 'Add a meta description to preview your search result.';
            if (titleOutput) titleOutput.textContent = title;
            if (descriptionOutput) descriptionOutput.textContent = description;
            if (urlOutput) urlOutput.textContent = window.location.origin + (preview.dataset.urlPrefix || '/') + (slugInput?.value || 'page-slug');
            if (titleCount) {
                titleCount.textContent = title.length;
                titleCount.classList.toggle('text-danger', title.length > 60 || title.length < 30);
                titleCount.classList.toggle('text-success', title.length >= 30 && title.length <= 60);
            }
            if (descriptionCount) {
                descriptionCount.textContent = description.length;
                descriptionCount.classList.toggle('text-danger', description.length > 160 || description.length < 70);
                descriptionCount.classList.toggle('text-success', description.length >= 70 && description.length <= 160);
            }
        };
        [titleInput, slugInput, metaTitleInput, metaDescriptionInput].forEach(input => input?.addEventListener('input', refresh));
        refresh();
    });
});
