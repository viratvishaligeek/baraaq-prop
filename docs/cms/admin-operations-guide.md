# CMS Admin Operations Guide

The CMS keeps the supplied Piller theme as the frontend design source of truth. The public homepage is the fixed Home 9 composition; administrators edit its content and settings without switching designs or using the retired page/widget builders.

## Dashboard

Use **Manage dashboard widgets** to enable or disable registered KPIs, choose approved internal destinations and set display order. Destinations are constrained to registered admin routes rather than arbitrary URLs.

## Properties

The property editor supports:
- Property type, category and amenities/features.
- Accommodation spaces with name, category, area, unit, description and included state.
- Repeatable accommodation entries with reorder controls and custom key/value attributes.
- Optional accommodation image uploads or selection from the Media Library.
- FAQ entries with ordering controls.
- Per-property CTA mode and WhatsApp details.
- SEO metadata, focus keyword and social image.

Accommodation images are represented by Media records. Referenced media is protected from deletion until its references are cleared. Public property pages render accommodation details, attributes and FAQs.

Property categories are managed through the shared category manager. Property types and amenities are derived from values used by property records; the taxonomy screens provide counts and links to matching properties.

## Blog, tags and comments

Posts support categories, multiple tags, public tag filtering, publishing workflow and SEO/social metadata. Tags have a dedicated CRUD screen at **Content management → Tags**. Public comments are throttled and remain pending moderation until approved.

## Pages and services

Pages and services use the standard content editor for create, update, draft preview and publishing. Draft previews do not publish content. Services have their own public listing and detail routes.

The retired universal page builder, reusable section library, widget library and service widget builder are not part of the active CMS. Historical migrations remain in the repository for upgrade history; do not manually re-run or delete them.

## Fixed Homepage 9

Open **Admin → Homepage content** at `/admin/homepages`.

The homepage has one fixed ten-section composition, in this order:

1. Hero — Home 9
2. About — mission and vision
3. Statistics — counter strip
4. Services — service cards
5. Featured properties — property listing
6. Featured property showcase — gallery slider
7. Home details — property facts
8. Team — agent carousel
9. Testimonials — trust score and reviews
10. Blog — latest articles

Each section has its own save operation and enabled/disabled control. Text, links, colors, limits, and registered image fields are edited from the section form. Rich text is sanitized before persistence. Image uploads create Media records and field attachments; leaving an image input empty retains its current image.

The **Homepage SEO** form edits the fixed homepage title and description. The header/contact form edits the website name, business details, contact information, navigation location, CTA and logo. The footer form edits footer copy, menu locations, map URL, gallery and social links.

Use **Website Settings** for global site-wide settings, and **Menus** to manage actual menu items. Changing the menu location in Homepage content does not create or edit menu items.

The public homepage is served at `/`. The contact page is served at `/contact` and posts to the existing throttled enquiry handler. The `/about` alias returns to the Home 9 about section.

## Media library

The Media Library supports multiple image/document uploads, alt-text editing, responsive variants for supported images, bulk selection/deletion, reference-aware deletion protection and a crop/resize editor that saves a new copy while preserving the original. The shared picker is available in supported content editors.

## SEO and indexing

Canonical URLs are generated from public routes for properties, posts, pages and services. The SEO overview highlights missing metadata and social images. The Sitemap / indexing screen shows the current robots policy and XML sitemap. The robots endpoint emits supported directives only and appends the sitemap URL automatically.

The XML sitemap includes the public homepage, listings and published property, post, page and service routes.

## Contact enquiries

The public contact form accepts either an email address or a phone number; at least one reply channel is required. The database migration `2026_11_23_000001_make_enquiry_phone_optional.php` makes the phone column nullable so email-only enquiries can be stored. Existing records are preserved. Rolling this migration back converts null phone values to empty strings before restoring the historical non-null constraint.

## Database and production migration notes

**Back up the production database before applying the fixed-homepage cleanup migrations or later schema changes.**

- `2026_11_22_000001_remove_legacy_page_builder_and_widget_schema.php` removes retired widget/reusable-section tables and related legacy data, removes obsolete homepage compositions and drops retired page/service section ownership. Its `down()` method intentionally does not reconstruct deleted data.
- `2026_11_22_000002_simplify_fixed_homepage_schema.php` removes legacy design/composition metadata and adds a unique constraint on `(homepage_id, section_key)`. Its `down()` method is intentionally irreversible.
- `2026_11_23_000001_make_enquiry_phone_optional.php` allows email-only enquiries. Its rollback preserves rows by converting null phone values to empty strings before restoring the non-null constraint.

Before running the unique-key migration against an existing database, check for duplicates:

```sql
SELECT homepage_id, section_key, COUNT(*)
FROM homepage_sections
GROUP BY homepage_id, section_key
HAVING COUNT(*) > 1;
```

Reconcile any returned rows before migrating. Review the migration status and take a tested backup first. Do not roll back across either cleanup migration; restore the verified backup if a recovery is required.

For a fresh installation, `DatabaseSeeder` creates the fixed Home 9 record and seeds the demo content and ten homepage sections. Do not run demo seeders against a live site unless you have reviewed their effects on the target database.

## Verification

Run the Laravel suite with:

```bash
php artisan test
```

The repository also defines a GitHub Actions workflow for the PHP suite and responsive browser checks. Confirm the actual workflow conclusion before release; the existence of a workflow definition alone does not mean the latest changes have been tested.
