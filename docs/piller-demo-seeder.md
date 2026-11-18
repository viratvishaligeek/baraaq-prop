# Piller demo content seeder

The default database seed now builds a realistic Pillar Realty demo site and selects the original Piller **Home 9** composition for the root URL.

## Commands

For a new, empty development database:

```bash
php artisan migrate:fresh --seed
php artisan storage:link
```

For an existing database, preserve its schema and records and add any missing demo content:

```bash
php artisan db:seed
php artisan storage:link
```

**Warning:** `migrate:fresh` drops every table. Use it only when you intentionally want to reset a local/development database. On an existing installation, use `php artisan db:seed`.

## What is populated

The seeders populate the existing CMS schema: property and post categories, properties and property galleries, posts and category pivots, pages, services, team members, testimonials, comments, enquiries, newsletter subscribers, banners, settings, menus and menu items, media records and section media attachments, reusable sections, all ten homepage template records, Home 9 homepage sections, widget areas, footer widgets and their assignments, page-view analytics, activity-log demo entries, roles and permissions.

The Home 9 composition follows the original HTML order: hero, About / mission and vision, statistics, services, filtered property grid, featured-property image slider, home details, team, testimonials and latest articles. It reuses the Piller theme assets and section views rather than replacing the design with generic placeholder blocks.

## Safety and repeatability

- The content seeder is idempotent: it uses stable slugs/keys and preserves existing matching content on reruns.
- It fills blank starter settings but does not overwrite non-empty settings configured by the site owner.
- It does not delete existing records or disable additional homepage sections.
- It selects Home 9 as the only default published homepage and clears a previously selected Page-as-homepage so the root URL displays Home 9.
- Theme images are copied into the existing public storage disk and registered in the Media Library. Ensure the public storage symlink exists.
- It does not create a fake administrator login. To create the real administrator through `DatabaseSeeder`, set `CMS_ADMIN_EMAIL` and `CMS_ADMIN_PASSWORD` in the environment before seeding.
- Demo enquiry emails and newsletter addresses use the reserved `.example` / `.test` domains and are not real contacts.
