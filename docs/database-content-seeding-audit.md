# Buraaqh Properties — Database and Content Seeding Audit

**Working branch:** `feat/database-content-seeding`  
**Audit baseline:** `main` at `4fab5bf21bb2d69f5c41fe967358393adef43ebc`  
**Scope:** migration history, Eloquent model/schema alignment, CMS relationships, seeders, media usage, and the Buraaqh Bareilly content baseline.

## 1. Migration inventory

The repository contains **39 migration files**:

- **3 Laravel framework migrations:** users, cache, and jobs.
- **36 application migrations:** 4 dated 17 November, 20 dated 18 November, 6 dated 19 November, 2 dated 22 November, 2 dated 23 November, and 2 dated 24 November 2026.

The application migrations are sequential feature slices rather than duplicate table-creation migrations. The first CMS migration creates the foundational content, taxonomy, access-control, menu, media, settings, enquiry, and property tables. Later migrations add homepage/page-builder structures, services, SEO fields, media variants, local content, tags, and service comments.

### Complete migration file inventory

**Laravel framework (3):**

- `0001_01_01_000000_create_users_table.php`
- `0001_01_01_000001_create_cache_table.php`
- `0001_01_01_000002_create_jobs_table.php`

**Application migrations (36), in execution order:**

- `2026_11_17_000000_create_cms_tables.php`
- `2026_11_17_000001_add_subject_to_enquiries.php`
- `2026_11_17_000002_add_preferred_contact_time_to_enquiries.php`
- `2026_11_17_000003_create_newsletter_subscribers_table.php`
- `2026_11_18_000001_create_homepages_table.php`
- `2026_11_18_000002_create_reusable_sections_table.php`
- `2026_11_18_000003_create_homepage_sections_table.php`
- `2026_11_18_000004_create_widgets_table.php`
- `2026_11_18_000005_create_widget_areas_table.php`
- `2026_11_18_000006_create_widget_area_items_table.php`
- `2026_11_18_000007_add_cta_configuration_to_properties_table.php`
- `2026_11_18_000008_add_section_identity_to_reusable_sections_table.php`
- `2026_11_18_000009_add_homepage_composition_completion_flag.php`
- `2026_11_18_000010_add_display_fields_to_categories_table.php`
- `2026_11_18_000011_create_media_attachments_table.php`
- `2026_11_18_000012_add_dynamic_links_to_menu_items.php`
- `2026_11_18_000013_create_services_testimonials_comments_and_seo_fields.php`
- `2026_11_18_000014_create_page_views_table.php`
- `2026_11_18_000015_rename_category_pivot_table.php`
- `2026_11_18_000016_add_publish_states_to_team_and_testimonials.php`
- `2026_11_18_000017_add_visit_sessions_and_activity_logs.php`
- `2026_11_18_000018_add_homepage_key_to_pages_table.php`
- `2026_11_18_000019_generalize_homepage_sections_for_pages.php`
- `2026_11_18_000020_create_four_footer_widget_areas.php`
- `2026_11_19_000001_add_dynamic_property_details.php`
- `2026_11_19_000002_add_focus_keyword_to_content.php`
- `2026_11_19_000003_add_media_image_variants.php`
- `2026_11_19_000004_add_service_page_builder_sections.php`
- `2026_11_19_000005_add_images_to_property_accommodations.php`
- `2026_11_19_000006_create_post_tags.php`
- `2026_11_22_000001_remove_legacy_page_builder_and_widget_schema.php`
- `2026_11_22_000002_simplify_fixed_homepage_schema.php`
- `2026_11_23_000001_make_enquiry_phone_optional.php`
- `2026_11_23_000002_create_local_content_and_service_detail_tables.php`
- `2026_11_24_000001_add_category_to_gallery_items.php`
- `2026_11_24_000002_create_service_comments_table.php`

### Findings

- No duplicate `Schema::create()` for the same application table was identified in the 36 application migrations.
- No duplicate column addition was identified. Repeated alterations to a table add distinct fields or relationships in later feature migrations.
- Foreign-key relationships are present for the principal relational records: role and permission pivots, property/category and property children, post comments and tag pivots, service features/images/comments, homepage sections, and media attachments.
- Polymorphic relationships (`categorizables`, `media_attachments`, `page_views`, and `activity_logs`) cannot have ordinary foreign keys to every possible target table; their target integrity is application-managed.
- The Laravel `sessions.user_id` field remains the standard nullable session reference rather than a hard user foreign key.
- Property deletion preserves enquiries by nulling `enquiries.property_id`; deleting a property cascades its property images, accommodations, and FAQs. Deleting a media record nulls optional accommodation image references and cascades its media-attachment rows.
- `homepage_sections` has a unique `(homepage_id, section_key)` constraint. The 22 November migration checks for duplicates before adding it.
- The category pivot rename migration is a conditional compatibility migration. Its rollback intentionally retains the current morph-pivot convention.
- `2026_11_22_000001_remove_legacy_page_builder_and_widget_schema.php` is deliberately a no-op. It documents that legacy widget/page-builder tables and their data are retained pending a separately approved export/retention decision; it does not drop them.
- `2026_11_18_000019_generalize_homepage_sections_for_pages.php` makes `homepage_id` nullable and adds page ownership. Its rollback deliberately refuses to proceed while page-specific rows exist.
- `2026_11_22_000002_simplify_fixed_homepage_schema.php` enforces the homepage section uniqueness rule after checking for duplicate rows.

### Migration decision

**No historical migration was consolidated, rewritten, or deleted. No new schema migration was added in this pass.** The repository does not provide a connected production database or a representative deployed migration table. It is therefore not possible to establish which historical migrations have already run in production. Editing or squashing those migrations would be unsafe without that evidence.

The current migration sequence is retained as the upgrade path. Any later physical removal of legacy widget/page-builder tables must be a separately reviewed forward migration after a production backup and a verified data-retention decision.

## 2. Model and relationship alignment

The audit identified model/schema mismatches in the homepage and page-builder area. The working branch addresses them:

- `Homepage`: mass-assignable design/default/composition and creator/updater fields, plus creator/updater relationships.
- `HomepageSection`: mass-assignable homepage/page/service/reusable-section references, instance identity and source design; explicit `page`, `service`, `reusableSection`, and `homepage` relationships.
- `Page`: `homepage_key` is fillable; author and page-section relationships are exposed.
- `Service`: exposes its homepage-section relationship.
- `ReusableSection`: a missing Eloquent model was added for the existing `reusable_sections` table, including its section and user relationships.

The existing Media Library architecture is retained: uploaded assets are represented by `media` records; content records keep their existing path-based fields; property accommodation images can reference `media.id`; homepage section images use the polymorphic `media_attachments` relation. No parallel media module or replacement image table was introduced.

## 3. Seeder architecture

`DatabaseSeeder` now runs the starter website seeders on a fresh installation regardless of whether the environment is local, testing, or production. The optional administrator account is created only when `CMS_ADMIN_EMAIL` and `CMS_ADMIN_PASSWORD` are configured; no default password or contact identity is invented.

The Buraaqh seeder orchestration is:

1. Retire known legacy template/demo records from public visibility without deleting their rows or media.
2. Seed missing property and blog categories.
3. Seed the six core website pages.
4. Seed editorial articles, FAQs, locality guides, and supported blog tags.
5. Seed service drafts and their feature details.
6. Seed primary and footer navigation menus.
7. Seed the ten Home 9 section records and safe image relationships.

Seeders use stable slugs/keys and `firstOrCreate` patterns. Exact old default copy is upgraded only when it still matches the known generated text; administrator-edited content is left alone. Existing menu items, section configuration, settings, category activation choices, and service content are not unconditionally replaced. The settings seeder replaces only recognised legacy template values (Pillar/Meerut branding, known fake contact/social placeholders, legacy map URLs, and known demo gallery paths); it preserves other administrator values, including intentionally blank settings. Only the exact old buy/sell menu URLs are migrated to the Buraaqh page destinations.

The legacy retirement seeder changes publication state only for known legacy template/demo identifiers. It does not delete records, service images, media files, enquiries, or newsletter subscribers. Old short editorial posts are retired only when their body still exactly matches the previous generated template copy.

## 4. Fresh-install content inventory

Expected starter records on an empty database after `php artisan migrate --seed`:

| Module | Expected records | Publication / safety |
|---|---:|---|
| Homepages | 1 | Home 9, default and published |
| Homepage sections | 10 | 5 enabled; 5 withheld until verified content exists |
| Categories | 13 | 8 property + 5 blog categories |
| Pages | 6 | About, Contact, Buy, Sell, Privacy, FAQ |
| Editorial posts | 7 | Long-form educational articles |
| Blog tags | 10 | Linked to relevant articles |
| FAQs | 18 | Property, documentation, consultation, commercial and visit questions |
| Neighborhood guides | 7 | Civil Lines, Rajendra Nagar, Izatnagar, Pilibhit Bypass, DD Puram, Mahanagar, Bareilly Cantt |
| Services | 6 | Draft pending business confirmation |
| Service features | 24 | Four scoped draft features per service |
| Menus | 4 | Primary, footer, footer quick links, footer support |
| Menu items | 25 | Stable, first-or-create navigation items |
| Media records | 6 | Existing bundled theme images, labelled illustrative |
| Media attachments | 6 | Homepage section image fields |

No property inventory, gallery photographs, testimonials, staff profiles, customer comments, enquiries, or newsletter subscribers are fabricated. Contact phone, email, WhatsApp, office address, and social URLs remain blank until supplied and verified by the administrator.

The seven editorial articles cover buying residential property in Bareilly, Uttar Pradesh property documents, residential versus commercial property, locality evaluation, registration/due diligence, residential selection factors, and questions to ask a real estate consultant. Each article is written as educational guidance and avoids legal guarantees, invented market statistics, and investment promises. The existing Home 9 section registry has no neighborhood-guide section/view; the seven guides are available through the seeded neighborhood-guide page and navigation. No new homepage widget was introduced because that would change the existing frontend composition.

## 5. Media mapping

The Home 9 seeder reuses bundled images already committed under `public/theme-assets`, imports them through the existing Media Library model, and creates `media_attachments` for the matching homepage fields. Alt text explicitly identifies the images as illustrative rather than actual Buraaqh listings.

| Homepage use | Existing asset |
|---|---|
| Hero background | `theme-assets/img/hero/hero-10-2.jpg` |
| Hero foreground | `theme-assets/img/hero/hero-9-right.png` |
| About primary | `theme-assets/img/why/why-choose-left-1.jpg` |
| About secondary | `theme-assets/img/hero/hero-8-left.jpg` |
| About tertiary | `theme-assets/img/contact/contact-img.jpg` |
| Home details decorative image | `theme-assets/img/hero/hero-10-3.jpg` |

The importer checks that a bundled image exists, is an image MIME type, and has minimum usable dimensions before creating a Media Library record. It uses a stable path-derived identifier and `firstOrCreate` so reruns reuse the same record. Existing administrator-selected images or custom paths are preserved.

No unverified template photography is assigned to property listings, service featured/inside images, blog featured images, locality guides, or the public gallery. Those content types remain without images until approved source imagery is available.

## 6. SEO and publication safeguards

- Page, post, service, and neighborhood-guide records receive page-specific SEO titles, descriptions, and focus keywords where supported.
- Global SEO defaults and Bareilly service-area metadata are seeded without adding fabricated contact details.
- The default Open Graph image remains empty unless a suitable existing Media Library record is available.
- Property listings remain empty until genuine inventory is supplied.
- Services remain drafts pending confirmation of the actual offering, scope, fees, and responsible professionals.
- Team, testimonial, gallery, and statistics sections are withheld rather than populated with invented people, reviews, achievements, or property photographs.

## 7. Database safety and verification status

No production database credentials or representative deployed database were available during this repository audit. The deployed `migrations` table, existing row counts, and real production data therefore could not be inspected. No production migration or destructive database command was run.

The repository's GitHub Actions workflow installs Composer dependencies, lints PHP, runs the Laravel test suite, prepares a fresh SQLite database, runs migrations and seeders, and exercises the 13 original HTML reference pages through browser tests. The database/content branch requires that workflow to run before this work can be called verified.

**Pending evidence:** fresh migration/seed execution output, repeated-seeder test results, full automated test result, and the PR check conclusion. At the time of this report, GitHub reports no check runs or commit statuses for the PR head, so the workflow has not supplied an execution result. This report must be updated with the actual run URL and commit after CI completes; it is not a claim that those checks have already passed.
