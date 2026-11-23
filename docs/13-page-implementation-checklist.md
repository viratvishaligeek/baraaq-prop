# Buraaqh Properties — 13-page HTML-to-Laravel implementation checklist

Source branch audited: `main` at `ab5bd8819c2c8922bed37b89044df5f427964391`.
Working branch: `fix/media-library-pagination-pages`.

The HTML files below were fetched from `html Template/` on `main` and inspected for their page headings, section hierarchy, header/footer classes, image references, IDs and JavaScript dependencies. A checkbox is intentionally left open until a complete visual, responsive, CMS-editing and interaction comparison has actually been performed.

## Reference-wide invariants

- [ ] Preserve the original `th-header`, navigation hierarchy, utility/cart elements, mobile menu, CSS classes and responsive breakpoints.
- [ ] Preserve the inner-page header: `th-header header-default`.
- [ ] Preserve the Home 9 header: `th-header header-default header-layout9`.
- [ ] Preserve the inner-page footer: `footer-wrapper footer-default`.
- [ ] Preserve the Home 9 footer: `footer-wrapper footer-layout1`.
- [ ] Preserve the original typography, theme variables, spacing, iconography, image crop/position and animation classes.
- [ ] Preserve original JavaScript behavior and dependencies: jQuery 3.7.1, `app.min.js`, `wow.min.js`, `nice-select.min.js`, `main.js`; page-specific slider, gallery, tab, filter, popup and accordion behavior must be verified.
- [ ] Use the exact reference image assets where they are decorative/template assets. Do not publish stock template photography as verified Buraaqh inventory or office photography.
- [ ] Bind content and image slots to CMS/database fields or managed settings. Do not substitute fabricated facts, inventory, prices, testimonials, people, office details or contact information.
- [ ] Compare desktop, tablet and mobile screenshots against the matching original HTML and record actual diffs.

## Page-by-page implementation checklist

### 1. `home-9.html` → `resources/views/frontend/homepage.blade.php`
Reference SHA: `f075f76eff230c46f098aba7c42822eaffb13e27`

- [ ] Home 9 header variant and navigation.
- [ ] Hero 9 background, right-side visual, eyebrow, headline and CTA.
- [ ] About collage: left/middle/right images, logo animation and copy.
- [ ] Mission and vision content.
- [ ] Four statistic counters.
- [ ] Services section (eight service tiles in source).
- [ ] Property category / popular property cards and location/bed/bath/area indicators.
- [ ] Featured property cards and carousel behavior.
- [ ] Home details / at-a-glance section.
- [ ] Team carousel.
- [ ] Testimonials carousel and review marks.
- [ ] Blog cards and carousel.
- [ ] Home 9 footer variant, footer gallery and navigation columns.
- [ ] Confirm each content-bearing section can be edited and hidden/shown from Homepage Admin without losing markup.

### 2. `about.html` → `resources/views/frontend/about.blade.php`
Reference SHA: `86157aa68d78e00e10a1a546b125dad334e7add1`

- [ ] Inner-page header and breadcrumb.
- [ ] About image collage and decorative shapes.
- [ ] Main about heading and description.
- [ ] Quality services / service feature cards.
- [ ] “See how … can Help” feature list (eight numbered features).
- [ ] Team section and carousel.
- [ ] Four statistics.
- [ ] Testimonials and review-source marks.
- [ ] Latest properties area.
- [ ] Property-goal CTA and footer.
- [ ] Replace legacy brand copy, demo people, metrics and listings with approved CMS records or suppress unverified data without removing the layout.

### 3. `property.html` → `resources/views/frontend/properties/index.blade.php`
Reference SHA: `40fe647c424386bfc17b690ed71ec42f387ba8f1`

- [ ] Breadcrumb and property listing title.
- [ ] List/grid tabs and original tab panel IDs.
- [ ] Sorting control and selected-state behavior.
- [ ] Property cards in grid and list variants, including image slider/popup.
- [ ] Listing type, location, bedrooms, bathrooms, area and price.
- [ ] Search and filter controls connected to query parameters.
- [ ] Sidebar search, featured listings and contact/banner widgets.
- [ ] Original pagination design with working previous/next/page links.
- [ ] Empty state must retain the original composition and must not invent inventory.

### 4. `property-details.html` → `resources/views/frontend/properties/show.blade.php`
Reference SHA: `3441233c8a89cebb26ef672467f03b8cf97f12de`

- [ ] Breadcrumb and property title.
- [ ] Main panorama/image slider and thumbnail navigation (five source slides).
- [ ] About this property, price and description.
- [ ] Property highlights: ID, type, rooms, bedrooms, bath, yard, purpose, area, parking, elevator, Wi-Fi and built-in details.
- [ ] Property gallery.
- [ ] Features and amenities.
- [ ] Floor-plan tabs: first floor, second floor, third floor and top garden.
- [ ] Location map, address and postcode.
- [ ] Related properties, search and contact sidebar.
- [ ] Every fact and image must come from the property record, related records or approved settings; no static demo property data.

### 5. `gallery.html` → `resources/views/frontend/gallery/index.blade.php`
Reference SHA: `2af7a74e6c7768eca0e183b06acc17eab1ffb8fb`

- [ ] Breadcrumb and “Amazing Gallery” title.
- [ ] “Stunning … Gallery” title/intro.
- [ ] Original category filter controls and filter classes.
- [ ] All twelve gallery cards, image crop, hover and popup behavior.
- [ ] CMS-backed category assignment and alt text.
- [ ] Original footer and responsive masonry/grid behavior.
- [ ] Verify filtering and pagination do not conflict.

### 6. `service.html` → `resources/views/frontend/services/index.blade.php`
Reference SHA: `ce49f43cb80d6c22edcbe60d2fcced17f3a28803`

- [ ] Breadcrumb and services heading.
- [ ] Nine numbered service cards and original image/icon placement.
- [ ] Featured image is independent from service-detail inside images.
- [ ] Service title, excerpt and destination are dynamic.
- [ ] Search, empty state and reference-styled pagination.
- [ ] Preserve responsive grid and animation classes.

### 7. `service-details.html` → `resources/views/frontend/services/show.blade.php`
Reference SHA: `fc0f93c464589569736684ea57646276fe7b9892`

- [ ] Breadcrumb and service detail heading.
- [ ] Service hero/featured image and original intro content.
- [ ] Detail body and highlights.
- [ ] “From Amazing Gallery” with independent inside-image relationship.
- [ ] Related service/property sidebar, search and contact CTA.
- [ ] Comments list and comment form only if backed by a real service-comment model; otherwise retain the designed section with a real supported workflow, not fabricated comments.
- [ ] Removing/replacing an inside image must not change the service featured image or delete a shared Media Library asset.

### 8. `neighborhood-guide.html` → `resources/views/frontend/neighborhood-guides/index.blade.php`
Reference SHA: `a55dd181d6293a072eee397dd4534cfb5bbf0737`

- [ ] Breadcrumb and “Explore The Neighborhoods” heading/intro.
- [ ] Locality cards (twelve source cards) with original imagery and hover treatment.
- [ ] Search field connected to the actual guide query.
- [ ] Locality, city, state, title, excerpt and hero image from CMS records.
- [ ] Original responsive grid and reference-styled pagination.
- [ ] Remove unrelated sample cities; only publish approved Bareilly locality content.

### 9. `faq.html` → `resources/views/frontend/faq/index.blade.php`
Reference SHA: `6219300eca3ca8aca5f72c8f05682672f755fcca`

- [ ] Breadcrumb and FAQ heading/intro.
- [ ] Original FAQ sidebar/contact area.
- [ ] All fifteen accordion rows and original collapse IDs/behavior.
- [ ] Search and category filters.
- [ ] Dynamic question/answer/category and structured data.
- [ ] Reference responsive layout and footer.

### 10. `error.html` → `resources/views/errors/404.blade.php`
Reference SHA: `1e17b1426f683acb356aaa4fd78e0bc23f0c596d`

- [ ] Preserve error background, illustration layers and 404 composition.
- [ ] Preserve title, error message and return-home action.
- [ ] Ensure actual 404 HTTP status.
- [ ] Verify unknown routes and missing slugs render this view.

### 11. `blog.html` → `resources/views/frontend/blog/index.blade.php`
Reference SHA: `9e281b552bbe295dc4840473a081362998ae8f8b`

- [ ] Breadcrumb and “Blog Grid” title.
- [ ] Twelve article cards, original grid spacing and image/date treatment.
- [ ] Author, category/tag, title, excerpt and CTA from post records.
- [ ] Search/category/tag filtering and query-preserving pagination.
- [ ] Sidebar/footer variants where present in the reference.
- [ ] No fabricated article cards or hardcoded post content.

### 12. `blog-details.html` → `resources/views/frontend/blog/show.blade.php`
Reference SHA: `68c500152face80dfdd0874c25d9efbd47938b3d`

- [ ] Breadcrumb and article title.
- [ ] Featured image and complete article content from the post editor.
- [ ] Author/date/category metadata.
- [ ] Procedure/list/quote/inline images only when supplied as part of the actual CMS article body or structured CMS fields.
- [ ] Dynamic tags and configured social destinations.
- [ ] Approved comments and moderation-backed comment form.
- [ ] Search, related articles and contact sidebar.
- [ ] Remove unrelated demo copy and fictional comments.

### 13. `contact.html` → `resources/views/frontend/contact.blade.php`
Reference SHA: `b0a96aa3173c9830d5587ba729dfc31056bde1b3`

- [ ] Breadcrumb and contact heading/intro.
- [ ] Contact feature cards: address, phone and email only when configured.
- [ ] Original contact illustration and layout.
- [ ] Contact form fields and validation.
- [ ] Enquiry persistence, spam/security protections and success/error states.
- [ ] Map only when a verified location/embed has been configured.
- [ ] Original footer and responsive layout.

## Existing Laravel architecture mapping

| Original HTML | Existing Blade destination |
|---|---|
| Home 9 | `frontend/homepage.blade.php` |
| About | `frontend/about.blade.php` |
| Property listing | `frontend/properties/index.blade.php` |
| Property details | `frontend/properties/show.blade.php` |
| Gallery | `frontend/gallery/index.blade.php` |
| Services | `frontend/services/index.blade.php` |
| Service details | `frontend/services/show.blade.php` |
| Neighborhood guide | `frontend/neighborhood-guides/index.blade.php` |
| FAQ | `frontend/faq/index.blade.php` |
| Error | `errors/404.blade.php` |
| Blog | `frontend/blog/index.blade.php` |
| Blog details | `frontend/blog/show.blade.php` |
| Contact | `frontend/contact.blade.php` |

## Work-session change log

### Session 1 — Media Library and pagination foundation

- [x] Created an implementation branch from current `main`.
- [x] Added a shared `vendor.pagination.buraaqh` view using the original `th-pagination` class structure.
- [x] Replaced the hardcoded pagination links on Services and Neighborhood Guides that pointed to unrelated listing routes.
- [x] Removed hardcoded duplicate page-number markup from Blog and Services/Neighborhood Guides; the Laravel paginator is now the single source of page URLs.
- [x] Connected Properties to the same styled paginator.
- [x] Added service detail gallery attachment from existing Media Library IDs, independently of the featured image field.
- [x] Added per-selected-image removal controls to the shared Media Library multi-select UI.
- [x] Removed direct file inputs from the general CMS content form where the Media Library picker already existed.
- [x] Replaced the service inside-image direct upload field with a multi-select Media Library collection; existing image rows retain individual detach checkboxes.
- [x] Replaced Gallery and Neighborhood Guide direct image uploads with Media Library selection and a remove action.
- [x] Removed unrelated hardcoded sample article body/tags from the Blog Details view; article content, categories, tags and configured social links now render from CMS/settings.
- [x] Added a gallery category column and Admin category selector; the original Apartment/Commercial/Land or Plot/Farm filters now filter database records server-side and preserve category through pagination.
- [x] Added a separate moderated service-comments table, public submission workflow, approved-only rendering and Admin moderation in the existing comments screen.
- [x] Replaced static property-detail slider photos with the listing's featured image and independent property gallery; removed fabricated property highlight values and template-photo fallbacks.
- [x] Replaced property listing duplicate stock-photo slides with actual listing gallery images; added price sorting, featured-first sorting and a real featured-listing sidebar.
- [x] Replaced service listing/detail stock-photo fallbacks; the service featured image is no longer used as a fallback inside the service-detail gallery.
- [x] Replaced Home 9 property carousel photos with each property's own media collection and connected its original category filter classes; team/testimonial/blog images and ratings now use actual CMS values or neutral empty states.
- [x] Replaced About page sample service cards, fake statistics, sample listings and placeholder contact details with published services, real record counts, published properties and configured contact settings.
- [x] Connected FAQ and Neighborhood Guide headings/intros to their seeded Admin settings; removed non-reference search controls and wired the original Neighborhood Guide search form.
- [x] Connected Contact enquiry subject options to active property categories and hide the map until a verified map URL is configured.
- [x] Added styled pagination to FAQ and Gallery and server-side gallery category filtering so the selected category remains active across pages.

### Execution environment and verification boundary

- Repository writes are available through the GitHub integration; all implementation commits in this session were written to `fix/media-library-pagination-pages`.
- A local checkout is not available: `git clone` failed with `Could not resolve host: github.com`.
- Local runtime: PHP 8.4.24, Node v22.16.0 and Chromium 144.
- Composer is not installed.
- PHP reports no PDO drivers, and the required SQLite/GD/mbstring/XML/cURL/ZIP extensions are absent.
- The GitHub integration can read and commit repository files but exposes no workflow-dispatch action. The repository workflow has not produced a run for this branch/PR. No automated test or browser test has been executed in this session.
- Therefore the tests added or updated below are **not verified as passing**. No screenshots were captured, and no visual comparison was performed.

## Verification still required

- [ ] Run `php artisan test` and the focused new/updated feature tests.
- [ ] Run PHP lint and Blade compilation.
- [ ] Run migrations and seeders against the supported test database.
- [ ] Exercise Media Library single/multiple selection, replace and remove flows in a real browser.
- [ ] Verify shared media files survive detaching from gallery/service records.
- [ ] Capture original and Laravel screenshots for all 13 pages at desktop/tablet/mobile widths.
- [ ] Compare both header variants and both footer variants.
- [ ] Verify all original JavaScript interactions, not just route rendering.
- [ ] Update each page checkbox only after its own acceptance criteria pass.

## Current page completion status

**No page is marked complete by this checklist yet.** The changes above are focused foundations; they do not substitute for page-by-page visual comparison and complete dynamic CMS verification.
