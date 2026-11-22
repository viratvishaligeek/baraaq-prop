# Fixed Home 9 CMS

## Runtime architecture

The public root route renders the published home-9 record only. The homepage has one fixed composition with ten registered section definitions and their original Home 9 Blade views. The section registry contains only those ten sections; retired homepage variants, Page Builder definitions, reusable sections and Widget rendering are not loaded.

The frontend keeps the original Home 9 Blade section structures and CSS classes. Dynamic content is resolved from the fixed section registry and persisted HomepageSection records. Missing keys in older section records fall back to the current registry defaults.

## Admin ownership

Administrators with settings.manage access use **Admin → Homepage** to manage homepage SEO, every section's text, links, visibility and images, site identity, logo, header menu location and call-to-action, contact details, footer content, footer menu locations, gallery images and social destinations. Actual menu items remain managed under **Admin → Menus**.

**Admin → Website settings** is reserved for global SEO defaults, robots directives, favicon, default social-sharing image, sticky-header behavior, theme colors and trusted global scripts. This avoids two different Admin screens competing to own the same homepage fields.

Section and menu links accept safe relative paths, fragments, telephone links, email links and absolute HTTP(S) URLs. Social profile and map links are restricted to HTTP(S). Unsafe schemes are rejected during validation and sanitized again when rendered.

## Media and persistence

Homepage section saves, homepage-wide settings, general website settings, CMS content uploads, property uploads and Media Library batch uploads use database transactions. Newly uploaded files are removed if their database transaction fails. Uploaded content and property images are registered in the Media Library. Media deletion checks content paths, homepage section attachments, page Open Graph images, property accommodation references and the footer gallery's stored media IDs before allowing deletion.

Replacing an image does not silently delete an existing Media Library asset. Legacy files that are not represented in the Media Library are only removed after their known content references have been checked.

## Admin tables

Admin listing tables use DataTables 2 with Bootstrap 5 and Responsive extensions from the official CDN. Tables backed by full result sets use DataTables search, sorting and pagination. Larger server-paginated lists retain Laravel pagination and use DataTables search/sorting on the current page; DataTables paging is disabled on those tables to avoid two competing pagination controls. Tables initialize once; no new npm dependency or frontend build step is required.

## Production migration caution

The repository contains 34 migration files. Existing migration history is retained rather than rewritten or deleted: reducing migration file count is not worth breaking environments where those migrations have already run.

The forward-only 2026_11_22_000001_remove_legacy_page_builder_and_widget_schema migration retires builder/widget records and schema. Its down() method intentionally does not reconstruct deleted data. Do not roll back across this migration: restore from a tested backup if reversal is required. Before applying it to a live database, verify its status with php artisan migrate:status, take and test a backup, and confirm the fixed Home 9 record and required section rows are present. Repository access cannot establish the migration state of a production database.

## Verification

Regression coverage has been added for all ten homepage section saves, registered image fields, safe links, partial-config defaults, rich-text sanitization, Admin route integrity, DataTables initialization, settings ownership, service menu links, media-reference protection and property image uploads/replacement.

The Laravel and Playwright suites still need to be executed in an environment with the repository dependencies installed. No npm build, Vite build, Webpack build, or frontend development server command is part of this work.
