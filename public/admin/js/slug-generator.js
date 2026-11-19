document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-slug-source]').forEach(slugInput => {
        const titleInput = document.querySelector(slugInput.dataset.slugSource);
        if (!titleInput) return;

        let manuallyEdited = Boolean(slugInput.value);
        const slugify = value => value
            .toString()
            .normalize('NFKD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');

        slugInput.addEventListener('input', () => { manuallyEdited = true; });
        titleInput.addEventListener('input', () => {
            if (manuallyEdited) return;
            slugInput.value = slugify(titleInput.value);
            slugInput.dispatchEvent(new Event('input', { bubbles: true }));
        });
    });
});
