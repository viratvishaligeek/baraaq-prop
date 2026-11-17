# Piller homepage section inventory

This inventory records the source HTML review for the ten original homepage designs. Source theme files and assets remain intact under `theme-files-html/`.

## Homepage-by-homepage section map

| Source | Distinct sections found in original HTML | Existing CMS/Blade mapping |
|---|---|---|
| `index.html` | Hero; featured and best-sale property grids; property-value cards; city exploration; agents; testimonials; articles | Ten-hero registry includes `hero.index`; property grids use `properties.index`; agents use `team.index`; articles use `blog.index`. The original testimonial and city-exploration layouts still need dedicated registered variants. |
| `home-2.html` | Hero; property categories; property grids; property-value gallery; agents; testimonials; articles; about collage; benefits; FAQ; app download; promotional marquee | Existing registered category, property, property-value, team, testimonial, blog, about, benefits, FAQ, download and marquee sections. |
| `home-3.html` | Map-search hero; categories; property grid; agents; trust-score testimonials; editorial articles; featured property gallery; neighborhood gallery; logos; FAQ; enquiry; benefits | Existing hero, category, property, team, testimonial, blog, featured-gallery, neighborhood/gallery, brand, FAQ, enquiry and benefits sections. |
| `home-4.html` | Hero; luxury property grid; editorial about/mission/vision; property-value cards; service/benefit cards; testimonials; agents; articles | Added reusable Piller about design `about.home-4`; service family uses the original icon-card visual language. Property, team, testimonial and blog families already exist. |
| `home-5.html` | Featured-property hero; room gallery; property-value cards; testimonials; agents; articles | Property-value gallery already exists. Room gallery and the original testimonial layout still need dedicated registered variants. |
| `home-6.html` | Corporate/project gallery; trust-score testimonials; feature/value cards; executive team; editorial blog grid | Added a services/feature-grid design `services.home-6`; existing team and blog families cover the corresponding data types. Dedicated project gallery and testimonial variants remain. |
| `home-7.html` | Hero; categories; property grid; editorial about; agents; testimonials; articles | Added reusable Piller about design `about.home-7`; existing category, property, team, testimonial and blog families cover the other data types. |
| `home-8.html` | Local-agency hero; property grid; customer process; local-agency about with statistics; agents; property-value gallery; testimonials; articles | Added reusable Piller about design `about.home-8`; property, team, property-value and blog families exist. Process and testimonial variants remain. |
| `home-9.html` | Agency hero; about/mission/vision; services; property grid; agents; trust-score testimonials; articles | Added reusable Piller about design `about.home-9` and database-driven service design `services.home-9`. |
| `home-10.html` | Modern hero; category/logo showcase; property grid; services; modern about with image pair and statistics; testimonials; agents; project gallery; articles | Added reusable Piller about design `about.home-10` and database-driven service design `services.home-10`. Added a tabbed category/property design based on the Home 10 category section. Project-gallery variants remain. |

## Designs added in this implementation checkpoint

- About: Home 4 editorial mission/vision, Home 7 split statement/image story, Home 8 local agency/statistics, Home 9 mission/vision triptych, Home 10 modern image panorama.
- Services: Home 4 icon cards, Home 6 editorial feature grid, Home 9 four-column service cards, Home 10 asymmetric modern service grid.

Added Testimonials designs: Home 1 classic centered slider, Home 4 editorial split cards, and Home 10 modern card grid. The About, Services and Testimonials designs use the existing Piller theme class system and theme asset paths. Their text, images, buttons, counters, visibility and spacing are controlled through the existing section `config` JSON. Services read published `services` records through the existing Eloquent model and shared section data resolver.


## Widget library inventory at this checkpoint

The registry now contains 53 design registrations across the requested widget families. The Home 2 and Home 7 property-category registrations share the same Piller slider template, so the inventory represents 52 distinct layout branches.

| Widget family | Registered designs | Layout coverage |
|---|---:|---|
| About Us | 6 | Home 2, 4, 7, 8, 9, 10 |
| Services | 4 | Home 4, 6, 9, 10 |
| Testimonials | 5 | Home 1, 2, 3, 4, 10 |
| Property Listings | 4 | Home 1, 2, 3 and featured-property carousel |
| Property Categories | 4 registrations / 3 layouts | Home 2/7 slider, Home 3 style-2 slider, Home 10 tabbed showcase |
| Team Members | 3 | Home 1, 2, 3 |
| Blog Posts | 3 | Home 1, 2, 3 |
| Image Galleries | 8 | Home 1 panoramic property explorer, neighborhood, featured property, property values, Home 5 room showcase, Home 6 project cards, Home 10 project showcase |
| FAQ | 3 | Home 2, 3 and original FAQ page |
| Call to Action | 4 | Home 2 download, Home 3 download, Home 3 enquiry, modern split banner |
| Statistics | 3 | Home 4, 6, 10 counter layouts |
| Contact | 3 | Contact cards, split map/details, conversion strip |
| Client Logos | 3 | Home 3 carousel, grid, marquee |

The Universal Page Builder now adds shared per-widget design controls for background color/image, heading and body colors, heading/body font sizes, and top/bottom padding. Image fields can be populated with a Media Library selection or a public image URL. Existing widget-specific content fields, responsive visibility controls and data-query settings remain available.

## Remaining conversion work

This is an inventory and progress record, not a claim that every section is migrated. Remaining work includes the Home 1 city-exploration composition, further original homepage-specific feature/benefit and project layouts, complete visual comparison of every widget against the source HTML at desktop/tablet/mobile breakpoints, and runtime testing. The original HTML files and theme assets remain untouched. Existing original HTML files and assets must remain until those layouts have been implemented and visually verified.
