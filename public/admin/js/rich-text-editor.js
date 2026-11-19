document.addEventListener('DOMContentLoaded', () => {
    if (!window.tinymce) return;

    document.querySelectorAll('textarea[data-rich-editor]').forEach(textarea => {
        const compact = textarea.dataset.richEditorCompact === 'true';
        tinymce.init({
            target: textarea,
            license_key: 'gpl',
            plugins: 'lists link image media table code searchreplace visualblocks wordcount anchor',
            menubar: compact ? false : 'edit insert format table',
            toolbar: compact
                ? 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image media table | blockquote | code'
                : 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image media table | blockquote | code',
            height: Number(textarea.dataset.richEditorHeight || (compact ? 240 : 420)),
            branding: false,
            promotion: false,
            file_picker_types: 'image',
            file_picker_callback: (callback, value, meta) => {
                if (meta.filetype !== 'image') return;
                window.dispatchEvent(new CustomEvent('open-richtext-media-picker', { detail: { callback } }));
            },
            setup: editor => editor.on('change keyup undo redo', () => editor.save()),
        });
    });

    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', () => {
            if (window.tinymce) tinymce.triggerSave();
        });
    });
});
