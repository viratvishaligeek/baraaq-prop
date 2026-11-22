# BaraaQ Prop — Laravel Real Estate CMS

A Laravel 13 real-estate CMS built with Blade, PHP, MySQL and the supplied Piller frontend theme. The original Piller design assets are served from `public/theme-assets/`; the fixed Home 9 composition is rendered by the existing Blade layout and section views.

## Requirements

- PHP 8.3+
- Composer 2
- MySQL 8+ or a Laravel-supported MySQL/MariaDB version
- PHP extensions required by Laravel 13, including Fileinfo, Mbstring, OpenSSL and PDO
- A web server configured to serve Laravel's `public/` directory

Node.js and npm are not required for the production runtime. The GitHub Actions workflow uses Node.js and Playwright for responsive browser tests.

## Installation

1. Install PHP dependencies:

   ```bash
   composer install --no-dev --optimize-autoloader
   ```

2. Create the environment file and application key:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. Configure `.env` for the hosting environment. Set `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL`, database connection details and unique administrator credentials:

   ```dotenv
   APP_NAME="BaraaQ Prop"
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-domain.example

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database
   DB_USERNAME=your_database_user
   DB_PASSWORD=your_database_password

   SESSION_DRIVER=file
   CACHE_STORE=file
   QUEUE_CONNECTION=sync
   FILESYSTEM_DISK=public

   CMS_ADMIN_NAME="Site Administrator"
   CMS_ADMIN_EMAIL=admin@your-domain.example
   CMS_ADMIN_PASSWORD="use-a-unique-password-of-at-least-12-characters"
   ```

4. Create the database and run migrations and seeders:

   ```bash
   php artisan migrate --force
   php artisan db:seed --force
   php artisan storage:link
   ```

5. Ensure `storage/` and `bootstrap/cache/` are writable by the PHP process. Set the web server document root to Laravel's `public/` directory.

6. Optional production optimizations:

   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

   Run `php artisan optimize:clear` before rebuilding caches after changing cached configuration or routes.

## Admin panel

- Login: `/admin/login`
- Dashboard: `/admin/dashboard`
- Homepage content: `/admin/homepages`
- Website settings: `/admin/settings`
- Administrator account: created by `DatabaseSeeder` from `CMS_ADMIN_NAME`, `CMS_ADMIN_EMAIL` and `CMS_ADMIN_PASSWORD`

The seeder creates Administrator and Editor roles, permissions and the editable primary navigation menu. It does not insert a hardcoded administrator password.

## CMS features

- **Properties:** property CRUD, categories, derived property types and amenities, search/filtering, featured images, galleries, per-property CTAs, accommodation spaces, custom space attributes, optional space images, FAQs and structured data.
- **Blog:** posts, categories, tags, public tag filtering, comments and moderation, publishing workflow and SEO/social metadata.
- **Pages and services:** standard content management, draft preview, publishing workflow, public CMS page routes and service listing/detail pages.
- **Fixed homepage CMS:** one fixed Home 9 design with ten editable sections. Administrators can update section content, links, registered image fields, visibility, homepage SEO, header/contact details and footer content without switching or rebuilding the original frontend design.
- **Dashboard:** configurable KPI collection, persistent enable/disable state, display order and safe internal destinations for property, content, enquiry, comment and analytics metrics.
- **Media library:** bulk selection/deletion, reference-aware deletion protection, alt-text management, responsive image variants, browser-side crop/resize editor and a shared Media Library picker for supported content editors.
- **Navigation and appearance:** grouped admin navigation, menu locations, nested menu items and the fixed Piller theme design system.
- **SEO:** metadata defaults, focus keywords, social images, route-generated canonical URLs, configurable robots.txt, XML sitemap and SEO completeness overview.
- **Administration:** users, roles, permission checks, enquiries, activity records, site settings and analytics.

The retired universal page builder, reusable section library, widget library and service widget builder are not part of the active CMS.

## Admin workflows

- Property types: `/admin/properties/types`
- Property categories: `/admin/content/categories?category_type=property`
- Property amenities: `/admin/properties/amenities`
- Blog tags: `/admin/tags`
- Media library: `/admin/media`
- SEO overview: `/admin/seo`
- Sitemap / indexing: `/admin/seo/indexing`
- Website settings: `/admin/settings`
- Homepage management: `/admin/homepages`
- Enquiries: `/admin/enquiries`

Dashboard KPI configuration is available from **Manage dashboard widgets**. Existing posts, pages and services can be previewed as drafts from their content editor; previewing does not publish them. KPI settings store enabled state, destination and display order.

## Public routes and theme

The public homepage is the fixed Home 9 Blade composition. Its ten registered sections are:

1. Hero
2. About / mission and vision
3. Statistics
4. Services
5. Featured properties
6. Featured property gallery
7. Home details
8. Team
9. Testimonials
10. Latest blog articles

Primary public routes:

- `/` — fixed Home 9 homepage
- `/contact` — contact form and enquiry submission
- `/about` — redirect to the Home 9 about section
- `/properties` and `/properties/{slug}` — property listings and details
- `/blog` and `/blog/{slug}` — blog listing and article
- `/services` and `/services/{slug}` — service listing and detail
- `/team` — published team members
- `/testimonials` — published testimonials
- `/{slug}` — published CMS pages

Legacy `index.html`, `property.html`, `property-details.html`, `blog.html`, `blog-details.html` and `home-9.html` URLs redirect to their current destinations.

## Production migration warning

The fixed-homepage cleanup migrations are intentionally irreversible:

- `2026_11_22_000001_remove_legacy_page_builder_and_widget_schema.php` removes retired widget/reusable-section tables, related legacy data and obsolete homepage compositions. Its `down()` method does not reconstruct deleted data.
- `2026_11_22_000002_simplify_fixed_homepage_schema.php` removes legacy design/composition metadata and enforces unique `(homepage_id, section_key)`. Its `down()` method throws rather than pretending to restore removed schema.

Before upgrading production, take and verify a full database backup, review the migration status and check for duplicate homepage sections:

```sql
SELECT homepage_id, section_key, COUNT(*)
FROM homepage_sections
GROUP BY homepage_id, section_key
HAVING COUNT(*) > 1;
```

Reconcile any duplicate rows before running migrations. Do not roll back across either cleanup migration; restore the verified backup if recovery is required.

For a fresh installation, `DatabaseSeeder` creates the fixed Home 9 record and seeds demo content and the ten homepage sections. Review demo seeder effects before running seeders against an existing live site.

## Operational notes and verification

- The public contact form requires at least one reply channel (email or phone). The `2026_11_23_000001_make_enquiry_phone_optional.php` migration supports email-only enquiries at the database level.

- Uploads use Laravel's `public` filesystem disk. Create the storage symlink with `php artisan storage:link`.
- Configure HTTPS and production database credentials before going live.
- Enquiry submissions are stored in the database. Configure outbound mail separately if enquiry notifications are required.
- Theme switching is intentionally disabled to preserve the supplied Piller design.
- Run the PHP test suite with `php artisan test`.
- GitHub Actions is configured for the Laravel suite and responsive browser checks of the fixed Home 9 homepage at desktop, tablet and mobile sizes. Confirm the latest workflow conclusion before release; a workflow definition alone does not prove that tests passed.
- Responsive media variants are generated in the browser with the Canvas API; the PHP runtime does not need GD or Imagick for this workflow.
- See `docs/cms/admin-operations-guide.md` for current module workflows and migration details.
