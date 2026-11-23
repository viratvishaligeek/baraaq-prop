# Local content CMS modules

This branch adds dedicated database-backed content modules for the Buraaqh Properties site. These are normal CMS modules; they do not use or restore the retired universal page builder.

## Modules and public routes

| Admin module | Permission prefix | Public route |
|---|---|---|
| Gallery | \`gallery.*\` | \`/gallery\` |
| FAQs | \`faqs.*\` | \`/faq\` |
| Neighborhood guides | \`neighborhood-guides.*\` | \`/neighborhood-guide\` and \`/neighborhood-guide/{slug}\` |

Admin screens are under \`/admin/site-content/{type}\`. They support create, edit, soft delete, search, filters and 25-record server pagination. Publication actions require the relevant publish permission.

## Gallery and neighborhood media

- Gallery records require an image when first created. The image can be replaced or individually cleared later.
- Neighborhood guides can have a hero image, editable alt text and display order.
- New uploads are registered in the existing Media Library.
- Replacing or clearing a content image does not directly delete a Media Library file. It remains available for reuse and can be deleted through Media Library only when no content references it.
- Existing media reference checks include gallery images, neighborhood hero images and service inside images.

## Service details

Services continue to use the existing Services module and slug workflow. Each service can manage:
- A JSON array of feature records with title, description and optional icon name.
- Multiple inside images.
- Individual removal of existing inside images.
- Featured image and existing SEO metadata.

Feature and image collections are stored in relational \`service_features\` and \`service_images\` tables and rendered on the public service detail page.

## Database and permissions

Migration: \`2026_11_23_000002_create_local_content_and_service_detail_tables.php\`

Models:
- \`GalleryItem\`
- \`FaqItem\`
- \`NeighborhoodGuide\`
- \`ServiceFeature\`
- \`ServiceImage\`

Run migrations using the deployment's normal reviewed migration process. Do not roll back deployed migrations in production.

The role/permission seeder creates the module permissions. Administrators receive all permissions through the existing role seeder.

## Buraaqh seed content

\`BuraaqhContentSeeder\` replaces the old Pillar/Meerut demo seeder. It seeds Buraaqh Properties defaults, unpublished service drafts and features, Bareilly-focused FAQs and cautious neighborhood due-diligence guides. It does not seed fabricated contact details, staff identities, testimonials, customer leads or published sample property listings. Gallery content and service photography are intentionally left empty until approved business imagery is supplied; stock template photos are not presented as real properties or staff.

Known synthetic records from the former Pillar demo are retired by exact identifiers: legacy sample property slugs are moved to draft, fake team entries are soft-deleted, synthetic testimonials are unpublished, synthetic comments are moved to pending, synthetic enquiries are closed and synthetic newsletter addresses are removed. Other content is not bulk-deleted.

The main \`DatabaseSeeder\` calls this content seeder only in local/testing environments or when \`SEED_DEMO_CONTENT=true\` is explicitly configured.

## Verification status

Feature tests are included in \`tests/Feature/LocalContentCmsTest.php\`, covering public rendering, publication visibility, Admin creation, media registration, service feature updates and individual image removal. They still need execution in a PHP/Composer-enabled checkout before deployment.
