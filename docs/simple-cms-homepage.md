# Fixed Homepage 9 CMS

The public root route renders the published home-9 record only. Homepage content is stored in the homepages and homepage_sections tables; the original Home 9 section Blade views remain responsible for the visual design.

Administrators with settings.manage access use Admin → Appearance & design → Homepage management to edit homepage SEO details and each registered Home 9 section's text, links, display options and images. Uploaded replacement images are stored on the public disk, registered in media, and attached to the relevant homepage section.

The editor is a fixed, structured form. It does not support adding, deleting, reordering, duplicating, or switching homepage templates.

The legacy page builder, section library, reusable-section system, front-end widget renderer, widget-area manager, and dashboard KPI widget configurator have been retired. Regular CMS Pages and Services continue to use their title/content fields.

A cleanup migration removes legacy builder/widget tables and page/service section references, and retains only the home-9 homepage record. Its down() method is intentionally non-restorative because retired widget and builder content is not recreated.

Admin listing tables inside .content-card use DataTables 2 with Bootstrap 5 and Responsive extensions loaded from the official CDN. No npm packages or frontend build steps are required.
