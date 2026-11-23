# Database seeding coverage and safety policy

This document supplements `database-content-seeding-audit.md` with the operational rules for deciding which tables receive starter data and which must remain empty on a clean installation.

## Seeder entry point and execution order

Run the supported entry point:

```bash
php artisan migrate --force
php artisan db:seed --force
```

For local preview, confirm `APP_ENV=local` (or `testing`) and leave `CMS_DEMO_PROPERTIES` unset. The fixture seeder uses the repository's existing `ThemeMediaImporter` and public storage disk; it skips missing/invalid source assets rather than writing broken image paths.

`DatabaseSeeder` runs role/permission reference data, optionally bootstraps an administrator only when both `CMS_ADMIN_EMAIL` and `CMS_ADMIN_PASSWORD` are configured, then runs the homepage template, theme defaults and Buraaqh content orchestration. The content orchestration orders legacy demo retirement before categories, pages, articles, locality content, service drafts, navigation and homepage sections.

Do not run individual content seeders out of order. In particular, the homepage seeder expects the Home 9 record and registered section definitions to exist, while editorial and property content expect their categories to exist. `BuraaqhDemoPropertySeeder` runs last so the property taxonomy, CMS and theme media are ready first.

On a local/testing environment, the 12 fictional Bareilly property fixtures and six illustrative gallery records are enabled by default. Set `CMS_DEMO_PROPERTIES=false` to suppress them. Production defaults to disabled; only set `CMS_DEMO_PROPERTIES=true` when deliberately preparing a clearly labelled demonstration environment, never a live inventory website.

## Tables that receive starter records

| Data area | Tables | Seed policy |
|---|---|---|
| Access control | `roles`, `permissions`, `permission_role` | Create Administrator and Editor roles, the permission catalogue and their intended permission assignments. |
| Optional identity | `users`, `role_user` | Create only the configured administrator. Never invent a default password or create an account with a public, hardcoded credential. |
| Taxonomy | `categories`, `tags`, `post_tag`, `categorizables` | Seed property/blog categories and editorial tags, then attach relevant content through the existing pivots. |
| Editorial CMS | `pages`, `posts` | Seed the six core pages and seven substantive, Bareilly/Uttar Pradesh-focused educational articles. |
| Local information | `faq_items`, `neighborhood_guides` | Seed practical FAQs and seven locality-specific guides; avoid unsupported price, appreciation or safety claims. |
| Services | `services`, `service_features` | Seed six clearly marked draft service descriptions and their scoped feature records. Do not publish unconfirmed service offerings. |
| Homepage | `homepages`, `homepage_sections` | Seed the fixed Home 9 composition and registered section configurations. Keep sections that require unverified business facts disabled. |
| Media | `media`, `media_attachments` | Import inspected theme assets once and reuse their public-disk paths for homepage decoration and local demo property/gallery images. Demo property/gallery alt text identifies the imagery as illustrative, not real inventory. |
| Navigation | `menus`, `menu_items` | Seed primary and footer navigation with stable locations and destinations. |
| Configuration | `settings` | Seed brand-neutral defaults and Bareilly context; leave business contact details and social links blank until supplied and verified. |

Expected clean-install starter inventory is documented in the main audit. The administrator record and its `role_user` association are conditional on valid administrator environment configuration.

## Tables intentionally not populated with fabricated records

| Tables / data | Reason |
|---|---|
| Production `properties`, `property_images`, `property_accommodations`, `property_faqs` | These remain empty unless `CMS_DEMO_PROPERTIES=true` is explicitly configured. Fictional listing prices, addresses, availability and ownership must never be published as genuine production inventory. |
| Local/testing `properties`, `property_images`, `property_accommodations`, `property_faqs` | `BuraaqhDemoPropertySeeder` creates 12 clearly labelled fictional Bareilly preview listings, 24 gallery images, 36 room/detail rows and 24 property FAQs. The property descriptions and image alt text disclose the demo status. |
| Local/testing `gallery_items` | Six published preview gallery records reuse inspected theme assets and explicitly state that they are illustrative, not actual Buraaqh property photographs. Production remains empty unless demo seeding is explicitly enabled. |
| `service_images` | No verified service-specific image mapping is available; unrelated theme stock imagery is not suitable evidence. |
| `team_members`, `testimonials` | Real staff/agent identities and authentic customer feedback must come from the business. The current schema has no property-agent pivot or agent/location master tables, so the seeder does not invent those relationships. |
| `comments`, `service_comments` | These are visitor-generated feedback records and should be created through the moderated application workflow, not impersonated by a seeder. |
| `enquiries` | Enquiries represent real prospective customers and must not be fabricated as leads. |
| `newsletter_subscribers` | Subscription requires an actual person's consent. Never seed synthetic subscribers. |
| `banners` | The active fixed Home 9 design uses registered homepage sections; no separate verified campaign banner is required for the starter site. |
| `page_views`, `activity_logs` | Operational analytics and audit history must reflect actual requests and actions. |
| `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `password_reset_tokens` | Framework/runtime state is created by the application when required, not by content seeders. |
| Legacy `reusable_sections`, `widgets`, `widget_areas`, `widget_area_items` | Retained for migration/data compatibility but retired from the active fixed-homepage CMS. Do not revive or populate these tables without an approved migration and product decision. |

The framework's `migrations` repository is maintained by Artisan and is not seeded manually.

## Repeatability and integrity regression

`tests/Feature/DatabaseSeederIntegrityTest.php` exercises the complete seeder twice against the test database, compares row counts for every table, checks SQLite's foreign-key integrity report and asserts that consent-based/user-generated records remain empty on a fresh installation.

The existing `BuraaqhContentSeederTest` additionally checks content quality, expected starter record counts, publication safeguards, media attachment behavior, legacy demo retirement and preservation of administrator edits.

Run the focused and complete suites with:

```bash
php artisan test --filter=DatabaseSeederIntegrityTest
php artisan test --filter=BuraaqhContentSeederTest
php artisan test
```

A test definition is not evidence of a successful run. Record the actual CI or local test result against the commit before marking seeding validation complete.

## Production safety

- Take and verify a database backup before applying migrations or seeding an existing production database.
- Do not run `migrate:fresh`, truncate tables or delete user data as part of the production seed workflow.
- The seeders use stable identifiers and intentionally preserve administrator-authored content; review any new update rule against that guarantee.
- Do not add sample contact identities, listings, customer reviews, enquiries or newsletter subscribers to the production seeding path.
- If demonstration transactions are later required, add a separately named, opt-in development-only fixture seeder and ensure its records cannot be published accidentally.
