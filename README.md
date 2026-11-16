# Baraaq Prop — Laravel Real Estate CMS

Laravel 13 real-estate listing website and CMS. The supplied Piller HTML theme is preserved under public/theme-assets; public views are server-rendered with Blade. The custom admin uses Blade, Bootstrap 5, jQuery, Toastr and SweetAlert2. No Node, Vite or Tailwind build pipeline is used.

## Requirements
PHP 8.3+, Composer 2, MySQL 8+/MariaDB 10.6+ (SQLite is suitable locally), and the PHP extensions required by Laravel.

## Manual installation
1. Install PHP dependencies: composer install --no-interaction --prefer-dist
2. Copy .env.example to .env and run php artisan key:generate.
3. Configure APP_URL, database, mail, session/cache/queue and filesystem settings.
4. Before seeding, set CMS_ADMIN_NAME, CMS_ADMIN_EMAIL and CMS_ADMIN_PASSWORD in .env. Use a unique password of at least 12 characters. The seeder creates the initial Administrator user only when both email and password are present.
5. Run manually:
   - php artisan migrate --force
   - php artisan db:seed --force
   - php artisan storage:link
6. Point the web server document root to public/. Ensure storage and bootstrap/cache are writable. In production use APP_ENV=production, APP_DEBUG=false, HTTPS and a secure APP_KEY.

## URLs
- Homepage: /
- Properties: /properties
- Property details: /properties/{slug}
- Blog: /blog
- CMS pages: /{page-slug}
- Admin login: /admin/login
- Admin dashboard: /admin

## Modules
Schema and model foundation covers users, roles, permissions, properties, property images, categories, posts, pages, media, menus/menu items, banners, team members, enquiries and settings. Admin flows include property CRUD, posts/pages/categories/banners/team CRUD, media uploads, settings, enquiries, menu editing, role/permission assignment and user management. Some theme-specific page variants and advanced appearance settings still need full integration.

Uploaded files use Laravel's public disk. Back up the database before applying migrations to an existing site. Never commit .env or credentials.

No npm install, npm build, Node.js, browser automation, automated test suite or deployment step is part of this work.
