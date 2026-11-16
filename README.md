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

- Property listings, categories, cover images, ordered galleries and property enquiries
- Blog posts, categories, pages and per-item SEO metadata
- Media library with alt-text editing
- Navigation menus with parent/child nesting and drag-and-drop ordering
- Homepage banners with active state and schedule windows
- Team members and website contact/SEO settings
- Users, roles and permission checks
- Appearance overview for the fixed Piller theme and an SEO completeness overview

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
- Automated tests, PHPUnit/Pest, browser automation, npm commands, asset builds and deployment are not part of the implementation workflow requested for this repository.
