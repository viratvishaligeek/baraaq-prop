# Fixed Home 9 CMS

The public root route renders the published \`home-9\` record only. The homepage uses ten registered section definitions and their original Home 9 Blade views. The section registry contains only the fixed composition; retired template variants and builder section catalogs are not loaded.

Administrators with \`settings.manage\` access use Admin → Appearance & design → Homepage management to edit SEO, every fixed section's text, links, visibility and images, plus site branding, contact details, footer content, menus and social destinations. Footer menu items remain managed through Admin → Menus. Section link fields accept safe relative paths, fragments, telephone links, email links and absolute HTTP(S) URLs.

Section saves and site-wide homepage settings are persisted in database transactions. Uploaded files are removed if the corresponding database transaction fails. Replaced media records remain in the media library rather than being deleted behind the administrator's back.

The editor is a fixed structured form. It does not support adding, deleting, reordering, duplicating or switching homepage templates. Regular CMS Pages and Services continue to use their existing content management workflows.

## Production migration caution

The existing \`2026_11_22_000001_remove_legacy_page_builder_and_widget_schema\` migration deletes legacy records and drops retired tables/columns. Its \`down()\` method intentionally does not reconstruct deleted data. Do not rewrite this historical migration if it has been executed in any environment. Before applying it to a live database, verify its status with \`php artisan migrate:status\`, take a tested backup, and confirm that the Home 9 record and required section rows are present. Repository access alone cannot establish the migration state of a production database.

Admin listing tables use DataTables 2 with Bootstrap 5 and Responsive extensions from the official CDN. No npm packages or frontend build steps are required.
