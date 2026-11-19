# CMS Admin Operations Guide

This guide describes the operational workflows added to the BaraaQ Prop CMS. The original Piller theme remains the frontend design source of truth; the CMS stores content and widget configuration without replacing the supplied design system.

## Dashboard

Open **Manage dashboard widgets** from the admin dashboard to configure the KPI collection.

Each registered KPI supports:
- Enable or disable without deleting its definition.
- An approved internal destination.
- An explicit display order.

The current collection includes total properties, published properties, blog posts, pages, users, total and pending enquiries, comments and pending comments, visits and page views. Destination options are constrained to registered admin routes rather than arbitrary URLs.

## Properties

The property editor supports:
- Property type and category.
- Property amenities/features as a repeatable list.
- Accommodation spaces with name, category, area, unit, description and included state.
- Unlimited accommodation entries, with add/remove and move-up/move-down controls.
- Custom key/value attributes for each space.
- Optional accommodation image uploads or selection from the Media Library.
- FAQ entries with ordering controls.
- Per-property CTA mode and WhatsApp details.
- SEO metadata, focus keyword and social image.

Accommodation images are represented by Media records. The original image and available responsive variants are stored separately. Media referenced by an accommodation cannot be deleted until the reference is cleared. The public property page renders the space image, details, attributes and FAQs.

Property types and amenities are derived from the values currently used by property records. Create or edit values in the property editor; the taxonomy screens provide counts and links to matching properties.

## Blog tags and comments

Tags have a dedicated CRUD screen at **Content management → Tags**. A post can be assigned multiple tags. The public blog listing accepts a tag filter and post detail pages render links to the matching tag-filtered listing.

Comments remain moderated through the existing comments module. Public comment submissions continue to use the existing throttled endpoint and moderation status.

## Pages, services and widgets

Pages use the universal page builder. Services have a service-specific builder backed by the same registered widget definitions and shared section persistence service.

Builder workflows include:
- Add a registered design variation from the widget library.
- Preview a variation in isolation before adding it.
- Configure widget fields and registered data sources.
- Duplicate, reorder, enable/disable and remove widget instances.
- Preview a draft without publishing it.
- Create a separate draft copy from a published service before making edits.
- Publish the draft when it is ready.

Service widget instances are stored using the service_id owner column on the shared section table. Existing page and homepage section ownership remains unchanged.

## Homepage section library

The section library is grouped by registered section family. Each variation links to an isolated rendered preview. Homepage section instances can be reordered, enabled/disabled and collapsed in the editor. Collapse state is remembered in the current browser.

The ten original Piller homepage source designs remain available. The builder uses registered section definitions and the shared widget renderer rather than embedding arbitrary HTML from the browser.

## Media library and rich text

The Media Library supports:
- Multiple image uploads and document uploads.
- Small, medium and large responsive image variants for supported images.
- Alt-text editing.
- Select-all, clear-selection and bulk deletion.
- Reference-aware deletion protection for content, property galleries, accommodation images, homepage sections and reusable widget attachments.
- A crop/resize editor that saves a new image copy and preserves the original.

The shared Media Library picker is available in property, content and widget rich-text editor image insertion. Editors use TinyMCE's image file picker callback to select an existing library image. The picker upload workflow generates responsive variants in the browser.

## SEO and indexing

Canonical URLs are generated from the public route for properties, posts, pages and services. Editors do not need to maintain a manual canonical URL field.

The SEO overview highlights missing metadata and social images. Global SEO settings include meta robots policy and editable robots.txt directives. The robots endpoint only emits supported directives (User-agent, Allow, Disallow and Crawl-delay) and appends the current XML sitemap URL automatically.

The XML sitemap includes the public homepage, public listings and published property, post, page and service routes.

## Data and migration notes

The implementation is additive:
- media gains nullable responsive variant paths.
- homepage_sections gains nullable service_id ownership.
- property_accommodations gains a nullable foreign key to the Media Library.
- tags and post_tag provide reusable post tagging.

Existing property, page, homepage and media records are retained. Run migrations before using the new workflows:

    php artisan migrate --force

## Verification

Run the Laravel suite:

    php artisan test

The GitHub Actions workflow additionally installs Playwright and checks the ten Piller homepage designs at desktop, tablet and mobile viewports. A successful PHP suite alone is not a substitute for the browser stage; verify the final workflow conclusion before release.
