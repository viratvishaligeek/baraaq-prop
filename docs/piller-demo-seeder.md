# Buraaqh Properties content seeder

The fixed Home 9 homepage is the root page. The current seeders create Buraaqh Properties site defaults and Bareilly-focused editorial content without inventing property inventory, contact details, staff identities, testimonials or customer leads.

## Commands

For a new local or test database:

```bash
php artisan migrate:fresh --seed
php artisan storage:link
```

For an existing database, use the reviewed migration process and then:

```bash
php artisan db:seed
php artisan storage:link
```

**Warning:** `migrate:fresh` drops every table. Use it only for a database that is intentionally being reset. Never run it against a shared or production database.

## Seeded content

- Fixed Home 9 homepage and its ten section records.
- Buraaqh Properties settings and Bareilly service-area defaults.
- Property and editorial categories.
- General property education articles, FAQs and cautious neighborhood due-diligence guides.
- Service records and relational feature drafts, unpublished until the business confirms the actual offerings.
- Navigation menus and footer defaults.
- Theme design assets where used as general UI decoration, registered in the Media Library.

The seeder does not publish fabricated property listings. It does not seed stock template photographs as real property/gallery/team/service images. The Gallery remains empty until approved business imagery is uploaded. Phone, email, WhatsApp and office address remain blank until supplied by the business owner.

## Repeatability and data safety

- Stable slugs and keys use `firstOrCreate` / `updateOrCreate` so reruns do not create duplicate starter records.
- Existing non-empty owner settings are preserved; blank starter values can be filled.
- The seeder does not bulk-rewrite arbitrary pages, posts, services or menu labels. Legacy synthetic data is retired only by exact identifiers.
- Known legacy sample properties are moved to draft rather than deleted. Synthetic team, testimonial, enquiry, comment and newsletter records are handled only through their known demo identifiers.
- Owner-managed content is not globally searched and rewritten for words such as “Pillar” or “Meerut”.
- The default `DatabaseSeeder` invokes `BuraaqhContentSeeder` only in local/testing environments or when `SEED_DEMO_CONTENT=true` is explicitly set.

Before production use, review the published status and content of every seeded page/service, add verified inventory and approved images, configure contact information, and execute the full test suite on the target database engine.
