# Piller Theme Fidelity Inventory

**Source branch:** `feat/property-cms-foundation`  
**Original source directory:** `theme-files-html/`  
**Converted Laravel views:** `resources/views/frontend/home.blade.php` and `resources/views/frontend/theme-pages/home-2.blade.php` through `home-10.blade.php`.

## Source-of-truth rule

The original HTML in `theme-files-html/` is authoritative for markup, layout, class names, spacing, responsive behavior, image selection, iconography, fonts, and interaction. Blade conversion must preserve that design. Dynamic data bindings may replace static content, but they must not flatten or redesign the layout.

All ten source files were fetched from the feature branch and inspected. The nine secondary homepage Blade views are present. The primary design is represented by `frontend/home.blade.php`.

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


## Asset issue discovered during source inspection

- The original HTML references `assets/js/app.min.js`, and the public copy is `public/theme-assets/js/app.min.js`.
- Both source and public copies currently return an empty file (zero bytes) with the same Git blob SHA. This is not a change introduced by the CMS work; the source copy on `main` is also empty.
- `assets/js/main.js` references Swiper, GSAP, ScrollTrigger, SplitText, Isotope and other theme plugins. The original page script list only includes jQuery, the empty `app.min.js`, WOW, Nice Select and `main.js`. The required vendor plugin implementations were not found among those HTML script references.
- Font Awesome CSS declares local Pro font files for brands, duotone, light, regular, solid and thin weights under `assets/fonts/fontawesome/`. The HTML also loads Inter and Plus Jakarta Sans from Google Fonts.
- This is a genuine frontend verification blocker: do not report carousel, animation, filtering, slider or related theme interactions as working until the missing vendor bundle/dependencies are located or replaced with compatible licensed assets. Do not silently fabricate a replacement bundle.

