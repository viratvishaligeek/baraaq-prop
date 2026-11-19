# BaraaQ Prop — Laravel Real Estate CMS

A Laravel 13 real-estate CMS built with Blade, PHP, MySQL, Bootstrap 5, jQuery, Toastr and SweetAlert2. The Piller HTML theme supplied in \`theme-files-html/\` is retained as the frontend foundation; its static assets are served from \`public/theme-assets/\`.

## Requirements

- PHP 8.3+
- Composer 2
- MySQL 8+ (or a compatible MySQL/MariaDB version supported by Laravel)
- PHP extensions required by Laravel 13, including Fileinfo, Mbstring, OpenSSL, PDO and PDO MySQL
- A web server configured to serve the Laravel \`public/\` directory

Node.js, npm, Vite, Webpack, Tailwind, frontend compilation and browser automation are not required.

## Installation

1. Upload/checkout the repository and install PHP dependencies:

   \`\`\`bash
   composer install --no-dev --optimize-autoloader
   \`\`\`

2. Create the environment file and application key:

   \`\`\`bash
   cp .env.example .env
   php artisan key:generate
   \`\`\`

3. Configure \`.env\` for the hosting environment. Set \`APP_ENV=production\`, \`APP_DEBUG=false\`, \`APP_URL\`, MySQL connection details and unique administrator credentials:

   \`\`\`dotenv
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
   \`\`\`

4. Create the MySQL database and run the migration and seeder manually:

   \`\`\`bash
   php artisan migrate --force
   php artisan db:seed --force
   php artisan storage:link
   \`\`\`

5. Ensure \`storage/\` and \`bootstrap/cache/\` are writable by the PHP process. Configure the web server document root to Laravel's \`public/\` directory.

6. Optional production optimizations after environment configuration:

   \`\`\`bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   \`\`\`

   Run \`php artisan optimize:clear\` before rebuilding caches after changing cached configuration or routes.

## Admin panel

- Login: \`/admin/login\`
- Dashboard: \`/admin\`
- Administrator account: created by \`DatabaseSeeder\` from \`CMS_ADMIN_NAME\`, \`CMS_ADMIN_EMAIL\` and \`CMS_ADMIN_PASSWORD\`
- The seeder creates the Administrator and Editor roles, permissions, and an editable \`primary\` navigation menu. It does not insert a hardcoded password.

## CMS features

- **Properties:** property CRUD, categories, derived property types and amenities, search/filtering, featured images, galleries, per-property CTAs, accommodation spaces, custom space attributes, optional space images, FAQs and structured data.
- **Blog:** posts, categories, tags, public tag filtering, comments and moderation, publishing workflow and SEO/social metadata.
- **Pages and services:** dynamic pages, reusable registered widget instances, service-specific widget builder, draft copies, preview, publish, reorder, duplicate, enable/disable and per-instance configuration.
- **Homepage builder:** all ten Piller homepage designs remain available as source designs; section library is grouped by section family, individual sections can be previewed, and homepage section instances can be reordered, collapsed, enabled, disabled and configured.
- **Dashboard:** configurable KPI collection, persistent enable/disable state, display order and safe internal destinations for property, content, enquiry, comment and analytics metrics.
- **Media library:** bulk selection/deletion, reference-aware deletion protection, alt-text management, responsive image variants, browser-side crop/resize editor and a shared Media Library picker for content and rich-text image insertion.
- **Navigation and appearance:** grouped admin navigation, menu locations, nested menu items and the fixed Piller theme design system.
- **SEO:** metadata defaults, focus keywords, social images, route-generated canonical URLs, configurable robots.txt, XML sitemap and SEO completeness overview.
- **Administration:** users, roles, permission checks, enquiries, activity records, site settings and analytics.

## Admin workflows

- Login: /admin/login
- Dashboard: /admin
- Property types: /admin/properties/types
- Property amenities: /admin/properties/amenities
- Blog tags: /admin/tags
- Media library: /admin/media
- SEO overview: /admin/seo
- Website settings: /admin/settings
- Homepage management: /admin/homepages
- Page builder: open an existing page and choose **Open universal page builder**
- Service builder: open an existing service and choose **Open service widget builder**

Dashboard KPI configuration is available from **Manage dashboard widgets**. It stores each KPI's enabled state, destination and display order. Section and widget variation previews open as individual rendered previews.

## Theme pages and routes

The original theme HTML is preserved in \`theme-files-html/\`. Converted theme pages are in \`resources/views/frontend/theme-pages/\`; reusable dynamic property, blog and CMS page views live under \`resources/views/frontend/\`.

Primary dynamic routes:

- \`/\` — homepage
- \`/properties\` and \`/properties/{slug}\` — property listings and details
- \`/blog\` and \`/blog/{slug}\` — blog listing and article
- \`/{slug}\` — published CMS pages
- \`/{template}.html\` — converted Piller theme pages

Legacy \`index.html\`, \`property.html\`, \`property-details.html\`, \`blog.html\` and \`blog-details.html\` URLs redirect to their dynamic listing destinations.

## Operational notes

- Uploads are stored on Laravel's \`public\` filesystem disk. Create the public storage symlink with \`php artisan storage:link\`.
- Configure a valid HTTPS URL and production database credentials before going live.
- Configure outbound mail separately if enquiry notifications are later required; enquiry submissions are currently stored in the database.
- The Appearance screen reports the active Piller theme. Theme switching is intentionally disabled to preserve the supplied design.
- Run the automated PHP test suite with `php artisan test` (or the repository's configured PHPUnit command).
- GitHub Actions also runs the Laravel feature/unit suite and responsive browser checks for all ten Piller homepage designs at desktop, tablet and mobile viewport sizes.
- Responsive media variants are generated in the browser with the Canvas API; the PHP runtime does not need GD or Imagick for this workflow.
- Review the CMS admin operations guide at `docs/cms/admin-operations-guide.md` for module workflows and implementation notes.
