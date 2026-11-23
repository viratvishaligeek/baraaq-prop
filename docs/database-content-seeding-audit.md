# Buraaqh Properties — Database and Content Seeding Audit

**Working branch:** `feat/database-content-seeding`  
**Audit baseline:** `main` at `4fab5bf21bb2d69f5c41fe967358393adef43ebc`  
**Scope:** migration history, Eloquent model/schema alignment, CMS relationships, seeders, media usage, and the Buraaqh Bareilly content baseline.

## 1. Migration inventory

The repository contains **39 migration files** and the migration set creates **48 distinct tables** (including Laravel framework tables):

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

A `MigrationArchitectureTest` regression test now scans the migration files and fails if the same table is created more than once. The current migration sequence is retained as the upgrade path. Any later physical removal of legacy widget/page-builder tables must be a separately reviewed forward migration after a production backup and a verified data-retention decision.

## 2. Model and relationship alignment

The audit identified model/schema mismatches in the homepage and page-builder area. The working branch addresses them:

- `Homepage`: mass-assignable design/default/composition and creator/updater fields, plus creator/updater relationships.
- `HomepageSection`: mass-assignable homepage/page/service/reusable-section references, instance identity and source design; explicit `page`, `service`, `reusableSection`, and `homepage` relationships.
- `Page`: `homepage_key` is fillable; author and page-section relationships are exposed.
- `Service`: exposes its homepage-section relationship.
- `ReusableSection`: a missing Eloquent model was added for the existing `reusable_sections` table, including its section and user relationships.

The existing Media Library architecture is retained: uploaded assets are represented by `media` records; content records keep their existing path-based fields; property accommodation images can reference `media.id`; homepage section images use the polymorphic `media_attachments` relation. No parallel media module or replacement image table was introduced.

## 3. Seeder architecture

`DatabaseSeeder` now runs the starter website seeders on a fresh installation regardless of whether the environment is local, testing, or production. The optional administrator account is created only when `CMS_ADMIN_EMAIL` and `CMS_ADMIN_PASSWORD` are configured through `config/cms.php`; set those environment values before running the seeder. No default password or contact identity is invented.

The existing `BuraaqhBlogSeeder` and `BuraaqhLocalContentSeeder` modules are retained and expanded rather than replaced with duplicate editorial modules. The blog seeder owns long-form articles and tags; the local-content seeder owns FAQs and neighborhood guides. `BuraaqhContentSeeder` orchestrates both after categories and pages are available.

The Buraaqh seeder orchestration is:

1. `BuraaqhLegacyDemoContentRetirementSeeder` retires known legacy template/demo records from public visibility without deleting their rows or media.
2. `BuraaqhCategorySeeder` seeds missing property and blog categories.
3. `BuraaqhPageSeeder` seeds the six core website pages.
4. `BuraaqhBlogSeeder` seeds seven articles and their category/tag links.
5. `BuraaqhLocalContentSeeder` seeds additional FAQs and seven locality guides, and upgrades only exact recognised legacy FAQ/guide copy.
6. `BuraaqhServiceSeeder` seeds six draft services and their feature details.
7. `BuraaqhNavigationSeeder` seeds primary and footer navigation menus.
8. `BuraaqhHomepageSeeder` seeds ten Home 9 section records and safe image relationships.

Seeders use stable slugs/keys and `firstOrCreate` patterns. Existing content is generally left unchanged; the explicit FAQ and locality-guide upgrades compare current content to a known legacy body before changing it. The settings seeder replaces only recognised legacy template values (Pillar/Meerut branding, known fake contact/social placeholders, legacy map URLs, known demo gallery paths, and exact bundled legacy logo/favicon paths); it preserves other administrator values, including intentionally blank settings. Only the exact old buy/sell menu URLs are migrated to the Buraaqh page destinations.

The legacy retirement seeder uses narrowly scoped updates against known legacy template/demo identifiers. Static inspection of all 13 seeder classes found no `delete()`, `truncate()`, `forceDelete()` or `updateOrCreate()` calls. It does issue scoped `update()` statements for those known legacy rows, and the editorial/locality seeders update only recognised legacy copy. The regression test now writes an administrator-edited FAQ answer directly through the database, reruns the full seeder chain, and compares before/after counts and uniqueness for FAQs, articles, categories, services, menus, menu items, media and media-attachment keys.

## 4. Fresh-install content inventory

Expected starter records on an empty database after `php artisan migrate --seed`:

| Module | Expected records | Publication / safety |
|---|---:|---|
| Homepages | 1 | Home 9, default and published |
| Homepage sections | 10 | 5 enabled; 5 withheld until verified content exists |
| Categories | 13 | 8 property + 5 blog categories |
| Website settings | 57 | Brand, header/footer, contact placeholders, SEO and theme defaults |
| Roles / permissions | 2 / 116 | Administrator and editor roles; stable permission slugs |
| Pages | 6 | About, Contact, Buy, Sell, Privacy, FAQ |
| Editorial posts | 7 | Long-form educational articles |
| Blog tags | 10 | Linked to relevant articles |
| FAQs | 18 | Property, documentation, consultation, commercial and visit questions |
| Neighborhood guides | 7 | Civil Lines, Rajendra Nagar, Izatnagar, Pilibhit Bypass, DD Puram, Mahanagar, Bareilly Cantt |
| Services | 6 | Draft pending business confirmation |
| Service features | 24 | Four scoped draft features per service |
| Menus | 4 | Primary, footer, footer quick links, footer support |
| Menu items | 27 | Stable, first-or-create navigation items |
| Media records | 6 | Existing bundled theme images, labelled illustrative |
| Media attachments | 6 | Homepage section image fields |

No property inventory, gallery photographs, testimonials, staff profiles, customer comments, enquiries, or newsletter subscribers are fabricated. Contact phone, email, WhatsApp, office address, and social URLs remain blank until supplied and verified by the administrator.

The seven editorial articles cover buying residential property in Bareilly, Uttar Pradesh property documents, residential versus commercial property, locality evaluation, registration/due diligence, residential selection factors, and questions to ask a real estate consultant. Each article is written as educational guidance and avoids legal guarantees, invented market statistics, and investment promises. The existing Home 9 section registry has no property-category or neighborhood-guide section/view; categories and guides are available through their existing routes and seeded navigation, while the property section displays only verified inventory. No new homepage widget was introduced because that would change the existing frontend composition.

## 5. Media mapping

The Home 9 seeder reuses bundled images already committed under `public/theme-assets`, imports them through the existing Media Library model, and creates `media_attachments` for the matching homepage fields. Alt text explicitly identifies the images as illustrative rather than actual Buraaqh listings.

| Existing media filename | Media record ID | Assigned content record / field | Intended visual purpose | Dimensions | Relationship status |
|---|---|---|---|---|---|
| `theme-assets/img/hero/hero-10-2.jpg` | Pending database execution | Home 9 `hero.home-9.background_image` | Decorative hero background; illustrative architecture | 310 × 800 | Seeder maps field through Media Library attachment; runtime ID/link check pending |
| `theme-assets/img/hero/hero-9-right.png` | Pending database execution | Home 9 `hero.home-9.foreground_image` | Decorative foreground home image; not a listing | 1079 × 946 | Seeder maps field through Media Library attachment; runtime ID/link check pending |
| `theme-assets/img/why/why-choose-left-1.jpg` | Pending database execution | Home 9 `about.home-9.image_primary` | About section supporting architecture image | 661 × 526 | Seeder maps field through Media Library attachment; runtime ID/link check pending |
| `theme-assets/img/hero/hero-8-left.jpg` | Pending database execution | Home 9 `about.home-9.image_secondary` | About section secondary residential image | 854 × 854 | Seeder maps field through Media Library attachment; runtime ID/link check pending |
| `theme-assets/img/contact/contact-img.jpg` | Pending database execution | Home 9 `about.home-9.image_tertiary` | About section supporting interior/lifestyle image | 972 × 893 | Seeder maps field through Media Library attachment; runtime ID/link check pending |
| `theme-assets/img/hero/hero-10-3.jpg` | Pending database execution | Home 9 `home-details.home-9.image` | Decorative Home Details image; not a listing | 270 × 400 | Seeder maps field through Media Library attachment; runtime ID/link check pending |

The importer checks that a bundled image exists, is an image MIME type, and is at least 240 × 180 pixels before creating a Media Library record. The six selected raster assets were checked for dimensions and visual role; all pass that threshold. It uses a stable path-derived identifier and `firstOrCreate` so reruns reuse the same record. Existing administrator-selected images or custom paths are preserved.

No unverified template photography is assigned to property listings, service featured/inside images, blog featured images, locality guides, or the public gallery. Those content types remain without images until approved source imagery is available. The Home 9 blog cards now retain their image area but display an “Article image pending” state instead of repeating the same unrelated theme stock image.

## 6. SEO and publication safeguards

- Page, post, service, and neighborhood-guide records receive page-specific SEO titles, descriptions, and focus keywords where supported.
- Global SEO defaults and Bareilly service-area metadata are seeded without adding fabricated contact details.
- The default Open Graph image remains empty unless a suitable existing Media Library record is available.
- Property listings remain empty until genuine inventory is supplied.
- Services remain drafts pending confirmation of the actual offering, scope, fees, and responsible professionals.
- Team, testimonial, gallery, and statistics sections are withheld rather than populated with invented people, reviews, achievements, or property photographs.

## 7. Database safety and verification status

No production database credentials or representative deployed database were available during this repository audit. The deployed `migrations` table, existing row counts, and real production data therefore could not be inspected. No production migration or destructive database command was run.

The repository's GitHub Actions workflow installs Composer dependencies, lints PHP, runs the Laravel test suite, prepares an empty SQLite database with `php artisan migrate:fresh --force`, runs the complete `DatabaseSeeder`, runs each of the eight content seeders individually in orchestration order, runs the complete seeder a second time, and loads all 13 original HTML reference pages at desktop, tablet and mobile viewports. Playwright captures reference and live screenshots, checks response codes, shared layout presence and horizontal overflow. It does not perform a pixel-difference assertion, so screenshot artifacts still require human visual review. The live property-detail and service-detail routes are skipped when no published record exists; the reference files are still captured, while the blog detail route is exercised with the seeded editorial content. The database/content branch requires that workflow to run before this work can be called verified.

**Pending evidence:** fresh migration/seed execution output, repeated-seeder test results, full automated test result, and the PR check conclusion. At the time of this report, GitHub reports no check runs or commit statuses for the PR head, so the workflow has not supplied an execution result. This report must be updated with the actual run URL and commit after CI completes; it is not a claim that those checks have already passed.


## 8. Editorial and service frontend inspection

### Seven articles

Static source inspection of each seeded article found the following body lengths and H2 section counts. Each has a specific introduction, practical buyer/seller guidance and a conclusion; all paragraphs within each article are distinct rather than repeated to pad word count.

| Article | Words | H2 sections | Editorial focus |
|---|---:|---:|---|
| A Complete Guide to Buying Residential Property in Bareilly | 778 | 10 | Buyer brief, total budget, site inspection, locality checks and title review |
| Important Documents to Check Before Buying Property in Uttar Pradesh | 816 | 10 | Title chain, land/revenue records, approvals, dues and agreement review |
| Residential vs Commercial Property: What Buyers Should Understand | 716 | 9 | Permitted use, ownership cost, financing and purpose-led comparison |
| How to Evaluate a Locality Before Purchasing a Home | 865 | 11 | Street-level visits, access, utilities and separating observation from assumptions |
| Property Registration and Due Diligence in Uttar Pradesh | 961 | 12 | Authority, title, agreement, registration preparation and handover |
| Important Factors When Choosing a Residential Property | 957 | 13 | Household fit, condition, ongoing costs and comparison scorecard |
| Questions to Ask Before Selecting a Real Estate Consultant | 866 | 11 | Scope, fees, verification, conflicts and written service terms |

The seeder sets a per-article SEO title (maximum 60 characters), meta description (maximum 155 characters) and focus keyword, and uses explicit lower-case hyphenated slugs. Regression assertions now check these limits, heading structure, conclusion presence and paragraph uniqueness. No editorial featured image is assigned: the available theme blog images are generic template photography, not verified article-specific Bareilly imagery. The existing blog card and detail templates show a neutral “Article image pending” placeholder instead of a misleading repeated photo.

### Seven locality guides

The guides were expanded with distinct conditional site-check sections rather than unsupported claims about prices, infrastructure, transport or investment. Current fresh-seed source lengths are: Civil Lines 462 words; Rajendra Nagar 446; Izatnagar 461; Pilibhit Bypass 458; DD Puram 442; Bareilly Cantt 484; Mahanagar 431. Their additional checklists cover mixed-use permissions, weekday household routines, conditional noise/vibration checks, bypass entry/exit and service-road access, block-level comparison, exact Cantt jurisdiction/land status, and renovation/maintenance permissions respectively. These are questions to verify at the exact property, not assertions that a particular facility or condition exists. The fresh-install regression threshold is now greater than 400 words per guide.

Existing administrator-edited guide bodies remain protected. The revised copy is used for fresh records; existing records are only upgraded when their body matches a known legacy seed body. If a database already contains the prior generated guide copy rather than the older legacy placeholder, the seeder intentionally will not overwrite it automatically; that copy needs an explicit reviewed CMS edit or a separately versioned content upgrade.

### Six service records and existing frontend

All six seeded service records remain `draft`, with 24 linked features (four per service). They have no featured image and no `service_images` rows because no approved service-specific assets exist. The existing service detail Blade view reads the card/hero image from `services.featured_image` and the inside gallery from the separate `service_images` relation; it renders features from `service_features`. A new feature test exercises a published sample service with two different image paths and asserts both render independently. The six seeded drafts are therefore not public service-detail pages yet; publishing them and assigning imagery requires business approval.

The blog detail view renders sanitized article HTML, title, metadata, tags and category links. The existing Home 9 blog section retains its original Blade structure/classes and uses the image-pending placeholder. Page detail rendering also sanitizes CMS HTML. These are source-level integrations and regression-test definitions; runtime route and responsive rendering remain pending the Actions/browser run.
