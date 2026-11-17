# Piller Theme Fidelity Inventory

**Source branch:** `feat/property-cms-foundation`  
**Original source directory:** `theme-files-html/`  
**Converted Laravel views:** `resources/views/frontend/home.blade.php` and `resources/views/frontend/theme-pages/home-2.blade.php` through `home-10.blade.php`.

## Source-of-truth rule

The original HTML in `theme-files-html/` is authoritative for markup, layout, class names, spacing, responsive behavior, image selection, iconography, fonts, and interaction. Blade conversion must preserve that design. Dynamic data bindings may replace static content, but they must not flatten or redesign the layout.

All ten source files were fetched from the feature branch and inspected. Their Git blob SHAs match the corresponding files on `main`, and the complete `assets/` directory tree SHA also matches `main`; the feature branch is using the same original design reference. The nine secondary homepage Blade views are present. The primary design is represented by `frontend/home.blade.php`.

## Homepage inventory

The inventory below records the major content blocks visible in the original HTML. It is a structural inventory, not a claim that every text node and image has passed visual regression.

| Source | Major source sections / layout landmarks |
|---|---|
| `index.html` | Hero 1, property search, popular properties, why choose us, featured listings, property values, gallery, explore cities, team, testimonials, blog |
| `home-2.html` | Hero 2, advanced search, categories, about, benefits, properties, city gallery, property values, team, app download, marquee, second property listing, testimonials, FAQ, blog |
| `home-3.html` | Hero 3, categories, properties, why choose us, team, gallery, app download, featured gallery, testimonials, FAQ, enquiry form, marquee, blog, brand strip |
| `home-4.html` | Hero 4, about, services, properties, featured CTA, property details/contact, virtual tour, testimonials, FAQ, team, marquee, enquiry form, blog |
| `home-5.html` | Hero 5, about, room details, property contact/details, property values, testimonials, team, enquiry form, marquee, blog |
| `home-6.html` | Hero 6, about, featured projects, why choose us, statistics, testimonials, feature highlights, team, enquiry form, marquee, blog |
| `home-7.html` | Hero 7, property categories, about, property listings, and additional agency/benefit content; the remaining page blocks require a line-by-line visual pass during conversion verification |
| `home-8.html` | Hero 8, property search, local agency/about, properties, buying process, team, why choose us, property values, testimonials, FAQ, blog |
| `home-9.html` | Hero 9, about, statistics, services, properties, gallery, property details/contact, team, testimonials, blog |
| `home-10.html` | Hero 10, advanced search, about, property types, properties, services, testimonials, team, projects, blog |

The ten pages have distinct hero variants and differing section order, composition, CSS classes and footer/header variants. They must remain separate presets. The builder must expose sections from every design and retain each section's source design identity.

## Existing Blade conversion status

- `home.blade.php` is the dynamic primary homepage and already receives property, banner, post and team collections from `HomeController`.
- `home-2.blade.php` through `home-10.blade.php` are present as converted Blade pages. Their top-level content blocks generally mirror the source composition, including non-`section` wrappers such as hero, about, search, gallery, download, FAQ and lead-form blocks.
- The secondary pages are still largely static presentation pages. The presence of Blade files and dynamic asset paths does not mean their visible content is already admin/database-driven.
- The existing `/{template}.html` route is a static preview/compatibility route. It is not itself a homepage builder or published homepage record.
- The primary route currently uses a single view and fixed data query. The new homepage record/selector is the first step toward CMS-driven homepage selection; section composition is a subsequent builder phase.

## Shared asset inventory

The source HTML pages reference the same Piller asset families, with per-design images and CSS background assets:

- CSS: `assets/css/app.min.css`, `assets/css/fontawesome.min.css`, `assets/css/style.css`.
- JavaScript: jQuery and theme/vendor scripts referenced in the HTML, including `assets/js/main.js`.
- Images: `assets/img/hero/`, `about/`, `property/`, `product/`, `gallery/`, `team/`, `testimonial/`, `icon/`, `bg/`, `blog/`, `pages/`, `brand/` and favicons.
- Typography and icons: theme font-family declarations (Plus Jakarta Sans and Inter), Font Awesome icon font/classes, and SVG icon assets.

The source `theme-files-html/assets/css/style.css` and `public/theme-assets/css/style.css` have the same Git blob SHA, so the main theme stylesheet is already reused rather than duplicated. The original `style.css`, `app.min.css`, and `main.js` were inspected at source level. HTML references and CSS background URLs were inventoried.

## Required fidelity checks still outstanding

The repository/API environment does not render a browser. The following must remain open verification tasks until run in a real Laravel/browser environment:

1. Side-by-side screenshots for each original HTML page and its Blade counterpart at desktop, tablet and mobile widths.
2. Every original image, SVG, icon font, webfont and vendor script resolves from the public asset path.
3. Header, footer, hero, search, carousel, tabs, filters, forms, accordions, menus, sliders and sticky interactions behave as in the source.
4. All ten templates have matching section order and no missing blocks.
5. Dynamic property/post/team/testimonial data preserves card count, image aspect ratio, spacing and responsive breakpoints.
6. All hardcoded text, demo contact details, placeholder cards, prices, external links and social URLs are either mapped to CMS settings/content records or intentionally retained as theme chrome with an explicit reason.

No pixel-perfect or browser-interaction verification is claimed by this inventory.


## Asset inventory and source/public parity

A recursive Git tree inventory of `theme-files-html/assets/` contains **476 files**:

- 3 CSS files: `app.min.css`, `fontawesome.min.css`, `style.css`.
- 5 JavaScript files: `app.min.js`, `main.js`, `nice-select.min.js`, `wow.min.js`, and `vendor/jquery-3.7.1.min.js`.
- 12 Font Awesome font files: six `.woff2` and six `.ttf` variants for brands, duotone, light, regular, solid and thin.
- 454 image assets across the about, agencies, background, blog, brand, contact, download, error, city, favicon, gallery, hero, icon, normal, pages, popular, product, project, property, service, shape, team, testimonial and why folders.

The recursive Git tree comparison confirms that all **476 source asset files have matching public copies** under `public/theme-assets/`: zero missing files, zero extra files and zero blob-SHA mismatches. This means the repository already reuses the original theme assets without a need to upload or duplicate them.

The original HTML loads Inter and Plus Jakarta Sans from Google Fonts and references the shared CSS/JS files listed above. `main.js` contains references to Swiper, GSAP, ScrollTrigger, SplitText, Isotope and other theme plugins. GitHub's directory metadata confirms `app.min.js` is a 1,116,141-byte file; the text-file fetch interface returned an empty text payload for this large file, so its contents could not be inspected through that interface. **Do not classify it as an empty or missing bundle.**

### Remaining asset verification

- Source/public file parity is verified by Git blob SHA, but binary images and font files were not individually decoded or visually rendered.
- The contents of the large `app.min.js` bundle still need inspection in a local checkout or browser.
- All ten pages still need desktop/tablet/mobile screenshot comparison and interaction testing. No browser fidelity result is claimed here.


## Detailed homepage section checklist (HTML-source pass)

**Counting rule:** the following total counts literal top-level-inventory candidates matched by the source HTML `<section>...</section>` wrappers. Hero regions are implemented as `<div>` wrappers in this theme and are listed separately. Header, footer, search overlays, mobile navigation, floating controls and nested cards are global/component landmarks, not counted as homepage content sections. Some pages use large `<div>` blocks instead of `<section>`; these are not included in the 57-wrapper subtotal and remain in the structural follow-up pass.

**Initial explicit section-wrapper total: 57 across ten source files.** This is an auditable lower-bound inventory, not a claim that 57 represents every content block in the ten designs.

| Homepage | # | Source wrapper / content family | Data expected | Current extraction/admin status |
|---|---:|---|---|---|
| index | 1 | Popular properties — first card presentation | Properties, categories, price/status, images, features, links | Not independently extracted |
| index | 2 | Featured luxury properties — alternate card presentation | Properties, images, price, badges, metadata, CTAs | Not independently extracted |
| index | 3 | Property values / counters | Editable heading, image, counters, labels/icons | Not extracted |
| index | 4 | Explore cities / location feature cards | Location records, images, descriptions, property counts, links | Not extracted |
| index | 5 | Team members | Team/agent records, photos, role, social links | Extracted in part; heading settings only |
| index | 6 | Testimonials | Testimonial records, avatar/logo, quote, rating, slider settings | Not extracted |
| index | 7 | Blog/news slider | Published posts, author/date/image, comments, CTA | Extracted in part; heading settings only |
| home-2 | 1 | Property categories | Property categories, icons, counts, links, decorative shapes | Registered as a source-specific category slider; data/form/render implemented; visual verification pending |
| home-2 | 2 | Featured properties — standard cards | Properties, images, price/status, features, actions | Not extracted |
| home-2 | 3 | Property values / expert-rated villas | Editable intro and property/value cards | Registered with four editable image/content cards and reusable card partial; visual verification pending |
| home-2 | 4 | Team members — home-2 card variant | Team records, images, socials, slider settings | Not extracted |
| home-2 | 5 | Featured property spotlight — alternate card | Featured property, location, beds/baths, image, CTA | Not extracted |
| home-2 | 6 | Testimonials — style 2 | Testimonial records, avatars, quote, slider | Not extracted |
| home-2 | 7 | Blog/news — style 2 | Posts, author/date, excerpts, images, links | Not extracted |
| home-3 | 1 | Property categories — style 2 | Categories, SVG icons, labels, counts, links | Registered as style-2 category slider; data/form/render implemented; visual verification pending |
| home-3 | 2 | Popular properties | Properties, price/status, feature icons, links | Not extracted |
| home-3 | 3 | Team members — home-2 variant | Team records, photos, social links | Not extracted |
| home-3 | 4 | Testimonials — rating/trust layout | Testimonials, rating summary, trust mark, avatars | Not extracted |
| home-3 | 5 | Blog/news — style 3 | Posts, author, date, excerpt, images, links | Not extracted |
| home-4 | 1 | Featured luxury properties — style 2 | Properties, price, status, images, feature details | Not extracted |
| home-4 | 2 | Virtual tour / featured locations | Location/property records, gallery images, labels, links | Not extracted |
| home-4 | 3 | Testimonials — style 4 | Testimonials, avatars, quote marks, heading | Not extracted |
| home-4 | 4 | Team members | Team records, image, role, socials | Not extracted |
| home-4 | 5 | Blog/news | Posts, authors, dates, images, CTA | Not extracted |
| home-5 | 1 | Room details gallery | Room/feature items, number, title, description, image, detail link | Not extracted |
| home-5 | 2 | Property values / craftsmanship gallery | Editable intro, value cards, images, links | Not extracted |
| home-5 | 3 | Testimonials — dark panel | Testimonials, role, avatars, quote, slider | Not extracted |
| home-5 | 4 | Team members | Team records, photos, role, socials | Not extracted |
| home-5 | 5 | Blog/news | Posts, author/date, images, CTA | Not extracted |
| home-6 | 1 | Featured projects | Project records, images, location, labels, links | Not extracted |
| home-6 | 2 | Testimonials — trust score layout | Testimonials, score, review count, trust mark | Not extracted |
| home-6 | 3 | Feature highlights / corporate values | Feature records, icons, headings, body copy | Not extracted |
| home-6 | 4 | Executive/team profiles | Team records, portrait, name, title, socials | Not extracted |
| home-6 | 5 | Blog insights — style 5 | Posts, images, title, metadata, links | Not extracted |
| home-7 | 1 | Property categories | Categories, icons, counts, links, decorative shapes | Registered as source-specific category slider; data/form/render implemented; visual verification pending |
| home-7 | 2 | Popular properties | Properties, price/status, images, feature icons, CTAs | Not extracted |
| home-7 | 3 | Team members | Team records, photos, role, social links | Not extracted |
| home-7 | 4 | Testimonials — trust score layout | Testimonials, score, review count, avatars, trust mark | Not extracted |
| home-7 | 5 | Blog/news | Posts, authors, dates, excerpts, images | Not extracted |
| home-8 | 1 | Popular properties | Properties, price/status, images, feature icons, CTAs | Not extracted |
| home-8 | 2 | Buying process steps | Ordered steps, icons/illustrations, titles, descriptions, background | Not extracted |
| home-8 | 3 | Local agent/team feature | Team records, featured portrait, images, contact links | Not extracted |
| home-8 | 4 | Property values / craftsmanship | Editable intro, value cards, images, links | Not extracted |
| home-8 | 5 | Testimonials — trust score layout | Testimonials, score, review count, trust mark, avatars | Not extracted |
| home-8 | 6 | Blog/news — style 6 | Posts, author/date, image, title, CTA | Not extracted |
| home-9 | 1 | Services — style 2 | Services, SVG icons, title, excerpt, detail links | Not extracted |
| home-9 | 2 | Popular properties | Properties, price/status, images, feature icons, CTAs | Not extracted |
| home-9 | 3 | Team members | Team records, photos, role, social links | Not extracted |
| home-9 | 4 | Testimonials — trust score layout | Testimonials, score, review count, avatars, trust mark | Not extracted |
| home-9 | 5 | Blog/news — style 6 | Posts, author/date, images, title, links | Not extracted |
| home-10 | 1 | Property type / design gallery | Category/project records, gallery images, titles, tabs, links | Not extracted |
| home-10 | 2 | Featured properties | Properties, price/status, images, feature icons, CTAs | Not extracted |
| home-10 | 3 | Modern housing services — style 3 | Services, images, headings, descriptions, links | Not extracted |
| home-10 | 4 | Testimonials | Testimonials, quote, brand logo, avatar, heading | Not extracted |
| home-10 | 5 | Team members — style 6 | Team records, featured images, profiles, socials | Not extracted |
| home-10 | 6 | Projects — tabbed gallery | Project records, category tabs, image sets, titles, descriptions, CTA | Not extracted |
| home-10 | 7 | Blog/news | Posts, metadata, images, title, links | Not extracted |

### Hero and shared chrome checklist

| Source family | Count | Required fields/settings | Current status |
|---|---:|---|---|
| Hero variants | 10 | Slides, title fragments, subtitle, image/background, overlay, decorative media, trust/social proof, CTA labels/URLs, search form visibility and fields, slider/animation options, responsive alignment | Ten hero partials exist; only limited heading settings are exposed. Not complete. |
| Header variants | To be counted by structural pass | Logo variants, top-bar contacts, social links, menu location, CTA, search/mobile controls, sticky behavior, responsive breakpoints | Shared/global header not yet fully CMS-configurable. |
| Footer variants | To be counted by structural pass | About copy, contacts, menu columns, social links, map, gallery, copyright, background assets | Not yet fully CMS-configurable. |
| Search/lead forms | To be counted by structural pass | Field visibility/order, labels/placeholders, property filters, submit label/action, validation, responsive layout | Not yet a reusable section configuration. |
| Non-section wrappers | To be counted by structural pass | About, stats, FAQ, app download, marquee, CTA, enquiry form, gallery and other div-based blocks | Not yet comprehensively inventoried or registered. |

### Per-design verification gates

Each checkbox is independent. A source section is not considered complete until all gates are checked against that exact design variant.

| Homepage | Source HTML inspected | All content blocks inventoried | Extracted to Blade partial | Section-specific admin form | Database/data-provider binding | Draft preview | Publish/default regression | Desktop/tablet/mobile comparison |
|---|---|---|---|---|---|---|---|---|
| index | [x] | [ ] | [ ] | [ ] | [ ] | [ ] | [ ] | [ ] |
| home-2 | [x] | [ ] | [ ] | [ ] | [ ] | [ ] | [ ] | [ ] |
| home-3 | [x] | [ ] | [ ] | [ ] | [ ] | [ ] | [ ] | [ ] |
| home-4 | [x] | [ ] | [ ] | [ ] | [ ] | [ ] | [ ] | [ ] |
| home-5 | [x] | [ ] | [ ] | [ ] | [ ] | [ ] | [ ] | [ ] |
| home-6 | [x] | [ ] | [ ] | [ ] | [ ] | [ ] | [ ] | [ ] |
| home-7 | [x] | [ ] | [ ] | [ ] | [ ] | [ ] | [ ] | [ ] |
| home-8 | [x] | [ ] | [ ] | [ ] | [ ] | [ ] | [ ] | [ ] |
| home-9 | [x] | [ ] | [ ] | [ ] | [ ] | [ ] | [ ] | [ ] |
| home-10 | [x] | [ ] | [ ] | [ ] | [ ] | [ ] | [ ] | [ ] |

**Current measured status after the latest builder work:** 57 explicit section wrappers identified; 13 registered section partials exist (10 hero variants plus primary-design properties, team and blog partials). The builder now renders typed, section-specific controls from the PHP registry; property/team/blog sections support database-driven records and selected display settings (such as item limits, filters or metadata visibility). Hero settings have a broader typed schema, but several hero variants still do not consume every exposed setting, and the original hero content is not fully data-driven. The section library can save a section configuration and insert linked reusable instances; local overrides are merged over shared configuration. Section duplication and authenticated draft preview are now implemented in source. No source section meets the strict completion definition across all its text, images, links, responsive settings, interactions and visual regression checks yet. Therefore the number of fully complete source sections remains **0**. This is a conservative status, not a claim that the partials have no dynamic behavior.

### Element-level inventory still required

The 57-wrapper pass records representative headings, paragraph samples, image paths and per-wrapper counts of links/buttons/inputs. It does not yet enumerate every nested text node, SVG path, icon class, responsive data attribute, CSS pseudo-element, CSS background URL or JavaScript interaction. Those require the next extraction pass against the original HTML and theme CSS/JS. The exact source file remains the authority for each field. No default text should be considered safely editable until its source node has been mapped to a schema field and rendered from the saved configuration/content record.

## Builder implementation progress (source review only)

The following builder capabilities now have source-level implementations on `feat/property-cms-foundation`:

- Typed section-field schemas are defined per registered section in `config/cms-sections.php` (text, textarea, URL/path, color, number, checkbox and select controls).
- The admin settings form renders each section's own field schema instead of a universal heading/description form.
- Section updates validate by field type, whitelist registered fields and merge partial updates rather than clearing previously saved configuration.
- Property, team and blog section partials consume selected settings and existing database records. The property section supports sale/rent/featured filtering and a bounded item count; team supports bounded count and email visibility; blog supports bounded count and author/date visibility.
- A draft section can be duplicated without changing the live homepage.
- An authenticated draft preview route renders the saved draft composition with the same current frontend data collections.
- Administrators can save a section's effective configuration into `reusable_sections`, then insert it into another draft. Homepage instances reference the shared library record and merge per-instance overrides over the shared configuration.
- A separate additive migration stores the stable section key and source design on reusable library records.

These are partial foundations. The reusable library does not yet have a complete standalone edit/version/history interface. The section catalog still does not include the 57 source wrappers, hero fields are not all consumed by their Blade partials, and draft duplication cannot yet reconstruct complete source section sequences. The preview currently uses the existing frontend layout and current query limits; it is not a substitute for responsive browser validation.

**No execution claims:** Composer, Artisan migrations/seeders, automated tests, route compilation, and browser screenshot comparisons have not been run in this environment.

### Live-homepage safety guard

An additive `is_composition_complete` flag now protects the production homepage while the source conversion is incomplete. The public controller only renders the custom section composition when this flag is true; otherwise it retains the selected original full-page Blade template. The admin cannot set an incomplete composition as the live default, and the homepage list explains why. This deliberately prevents a draft containing only a hero or a few extracted sections from replacing the complete existing homepage. The flag must only be enabled after the per-design checklist and regression gates are actually complete.


### New div-based extraction pass — category and Home 2 about blocks

The next implementation pass confirmed that important content is not bounded by literal section tags. In particular, Home 2's about composition is a large `div.about-1-wrapper` block with a five-image collage, animated circular logo, four icon/feature cards, CTA and telephone action. It is now registered as `about.home-2`, rendered with the original `about-1-wrapper`, `img-box1`, `about-left/middle/right`, `about-1-item` and CTA class structure, and has section-specific controls for all five collage images, logo, four feature icons/titles/descriptions, heading/body, CTA and phone details. The section is included after the category slider when a Home 2 draft is initialized.

The category blocks are registered separately as `categories.home-2`, `categories.home-3` and `categories.home-7`. The Home 3 variant keeps `category-sec-2`, `category-card style-2` and its pagination; Home 2/Home 7 keep `category-sec-1`, source decorative shapes and the standard card. The resolver binds active property categories and published property counts. Category records now support an icon path, sort order and active state. These are source-level implementations; no responsive/browser pass has been run.

The builder's image fields now open a searchable picker backed by the existing Media Library. The endpoint returns only image MIME types and uses the configured storage disk URL. Category record icons can also be uploaded from the category editor. The registry now loads category and about definitions from dedicated files under `config/cms-sections/`, rather than adding all new definitions to the root registry file.

### Homepage verification matrix — current source-level status

Counts below are **minimum identified literal section wrappers** plus separately registered partials. They do not count every div-based block, slider, card collection, search overlay or shared chrome element. “Form / binding / render” records source implementation only, not runtime proof. Responsive verification remains pending for every design.

| Homepage | Literal wrappers identified (minimum) | Registered section partials | Section forms with matching schema | Database bindings in registered sections | Frontend partial rendering | Responsive verification |
|---|---:|---:|---|---|---|---|
| index | 7 + hero | 4 (hero, properties, team, blog) | Partial; hero still incomplete | Banner, properties, team, posts | Source-level only | Pending |
| home-2 | 7 + hero, plus about, benefits, neighborhood, download, marquee and FAQ div blocks | 13 (hero, categories, about, benefits, property grid, neighborhood gallery, property values, team, download, marquee, featured property carousel, testimonials, FAQ) | Twelve non-hero section schemas plus partial hero settings | Property categories/counts, published properties and active team records | Source-level only | Pending |
| home-3 | 5 + hero | 2 (hero, categories) | Category schema implemented; hero partial | Property categories/counts | Source-level only | Pending |
| home-4 | 5 + hero | 1 (hero) | Hero partial only | None verified for hero | Source-level only | Pending |
| home-5 | 5 + hero | 1 (hero) | Hero partial only | None verified for hero | Source-level only | Pending |
| home-6 | 5 + hero | 1 (hero) | Hero partial only | None verified for hero | Source-level only | Pending |
| home-7 | 5 + hero | 2 (hero, categories) | Category schema implemented; hero partial | Property categories/counts | Source-level only | Pending |
| home-8 | 6 + hero | 1 (hero) | Hero partial only | None verified for hero | Source-level only | Pending |
| home-9 | 5 + hero | 1 (hero) | Hero partial only | None verified for hero | Source-level only | Pending |
| home-10 | 7 + hero | 1 (hero) | Hero partial only | None verified for hero | Source-level only | Pending |

**Strict completion count remains 0.** The table deliberately distinguishes registered partials from fully verified source sections. No homepage has a complete source-block inventory, full section sequence, complete settings coverage, and desktop/tablet/mobile visual regression result yet.


The Home 2 property-values gallery is now also registered as `property-values.home-2`. Its source-specific three-column composition (left card, two stacked center cards, right card), hover overlay, plus icon, image and per-card title/description/button/URL are editable. The repeated card markup lives in a small reusable Blade partial. This section is configuration-driven rather than backed by a new database content type.

The Home 2 neighborhood gallery is another extracted div-based component (`gallery-sec-2`). Its original `gallery-card style-2` image overlay, city/count labels, links, decorative shape, slider controls and breakpoint behavior are retained. All eight card images, labels, titles and URLs have individual section settings, with shared slider loop/column and spacing/background controls. This is editable configuration content; it is not yet backed by a dedicated location entity.

The Home 2 agent carousel is now registered as `team.home-2`. It retains the original `team-area-1`, `team-card style-2`, decorative team shape, source Swiper breakpoints, arrows, email/social actions and chat invitation. Its resolver loads active `TeamMember` records in configured order; the team editor now exposes social profile URLs as a validated JSON object so those links can be rendered without hardcoded demo URLs. This section's responsive behavior is represented by the source breakpoints and visibility controls, but browser verification is still pending.

The Home 2 `why-sec-2` benefits block is now registered from its original div-based markup. Its three `why-card-1 style-2` cards retain the hover icon, numbered label, feature icon, text and full-width CTA. Each card's content/icon/link is independently editable; the section exposes desktop columns, spacing, decorative media, background, animation and responsive visibility controls.

The Home 2 `popular-sec-1` listing is now a database-backed property grid using the original `popular-list-1` card, image slider, badge, metadata and filter-menu structure. The section resolver loads published properties with images/categories plus category filters; section controls cover item count, listing filter, columns, category filters, image sliders, card actions, badge, CTA, spacing, background, animation and responsive visibility. Favorite persistence is not implemented by this section yet, and no browser comparison has been performed; therefore it is not marked complete.

The Home 2 featured-property carousel is a separate registered variant from the preceding filtered property grid. It preserves the source `popular-sec-1`, `popular-list-1 style-2`, two-slide Swiper layout, metadata, agent strip and previous/next controls, while resolving published property records, images and creator names. Its carousel controls and listing filters are section-specific. The Home 2 testimonial slider and five-item FAQ accordion are also registered with per-review/per-question settings, avatar/media selection, trust-summary fields, star ratings, unique accordion IDs and source-specific layout classes.

Home 2's current draft initializer now includes the thirteen currently registered blocks in the identified source order. It is still not the complete original sequence: additional source blocks/components and shared header/footer/search behavior remain to be inventoried and converted before the design can pass the completion gate.
