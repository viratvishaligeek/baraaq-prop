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

