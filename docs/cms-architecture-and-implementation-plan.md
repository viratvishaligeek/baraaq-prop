# BaraaQ Prop CMS — Architecture Audit and Implementation Plan

**Target branch:** `feat/property-cms-foundation`  
**Architecture baseline:** Laravel 13, PHP 8.3+, Blade, MySQL, Bootstrap 5, jQuery. No Node/Vite/SPA requirement.  
**Theme source of truth:** `theme-files-html/` (the ten original Piller homepages and their original assets).

## 1. Executive audit

The feature branch already contains a meaningful Laravel CMS foundation. The implementation should extend it, not replace it.

### Repository facts verified on the feature branch

- The original theme source exists in `theme-files-html/`. All ten homepage HTML files were fetched and inspected: `index.html` and `home-2.html` through `home-10.html`.
- The branch also contains a primary dynamic homepage at `resources/views/frontend/home.blade.php` and converted Blade pages for `home-2` through `home-10` under `resources/views/frontend/theme-pages/`.
- The source HTML uses the shared Piller CSS/JS stack, including `assets/css/app.min.css`, `assets/css/fontawesome.min.css`, `assets/css/style.css`, and `assets/js/main.js`. The source `style.css` is present in `public/theme-assets/css/style.css` with the same blob SHA, confirming the public theme asset copy is already in place. Keep reusing it.
- The ten original pages are not identical layouts. The inspected landmarks include different hero/header variants, property/category sections, services, process/value sections, team, testimonials, project cards, blog sections, and distinct footer variants. Some designs share classes but have different composition and order.
- Existing Laravel routes expose `/`, property and blog routes, published CMS pages, and static `/{template}.html` theme-page routes. `ThemePageController` returns a static Blade view; it does not load homepage configuration.
- `HomeController` currently supplies a fixed set of properties, banners, posts, and team members to one fixed homepage view.
- Existing property detail view stores and renders an enquiry form and also renders a WhatsApp link when the global WhatsApp setting exists. There is no per-property CTA mode/number/message configuration.
- Existing menu editing supports drag reorder and parent selection, but menu items only store a label, URL, target, order and active flag. The cached menu eager-load path is fixed to five nested child levels.
- Existing dashboard data is operational counts and recent records. It is not a traffic analytics or admin activity dashboard.
- The CMS schema is currently concentrated in `2026_11_17_000000_create_cms_tables.php`. It creates the existing CMS tables and alters users in one migration rather than using a separate migration per new table.
- `routes/web.php` currently contains frontend and admin routes together in a compressed file. There is no separate `routes/admin.php` in the inspected branch.
- The README confirms the intended no-Node workflow and documents the existing property, blog, page, media, menu, banner, team, user/role, settings, and SEO features.

### Scope and verification boundary

This is a repository/API audit, not a successful local Laravel run. The available execution environment previously had PHP but no Composer, and the repository could not be cloned because outbound DNS/network access failed. GitHub source reads and GitHub-side commits are available. Artisan commands, migration execution, PHPUnit/Pest, and browser rendering must therefore be reported as **not run** unless a working execution environment becomes available.

## 2. Existing modules versus required capabilities

| Existing module | Current implementation | Required upgrade |
|---|---|---|
| Authentication / users / roles / permissions | Login, user/role records and permission middleware exist | Preserve; audit authorization coverage, active-user enforcement, validation and admin activity logging |
| Properties | CRUD, category, price/location/features, cover image, ordered gallery, publish status | Preserve; add per-property CTA mode and WhatsApp configuration, stronger filters/validation, structured reusable frontend property cards and SEO |
| Property enquiries | Form submissions linked to a property, status and notes | Preserve; make the form CTA conditional, retain enquiry storage, improve statuses/assignment/notification workflow without breaking existing submissions |
| Posts / categories | Blog content and polymorphic categories | Preserve; improve editor/media/SEO/publishing and category relationships |
| Pages | Slug, content, template, status and SEO | Preserve; connect template/sections and all visible page content to editable records |
| Media | Upload metadata, disk/path, MIME, size and alt text | Preserve; add safe validation, usage tracking and media picker reuse |
| Navigation | Menus, nested items, drag ordering, active flag | Upgrade to internal route/entity selectors, menu locations, safe external links, icons/classes, unlimited reasonable depth, recursive rendering and persistent order |
| Banners | Placement, schedule, order and active flag | Preserve; make all banner/hero fields configurable and reusable by homepage sections |
| Team | Member records and ordering | Preserve; connect all team sections and social links to records |
| Settings / SEO | Key-value settings, SEO overview | Preserve; add typed/grouped configuration, cache invalidation, per-page metadata, social/canonical/robots controls |
| Appearance | Fixed-theme overview | Upgrade into homepage/template/section management; do not add theme switching that discards Piller design |
| Dashboard | Counts and recent records | Add privacy-conscious page-view metrics, popular pages, enquiry funnel and admin activity |
| Homepage management | One fixed dynamic home view plus static converted home pages | New homepage records and builder supporting all ten source designs, section composition, draft/publish and persistent ordering |
| Reusable sections | Not present as a CMS module | New reusable section presets and section instance configuration |
| Widgets | No general widget system found | New registered widget types, instances, widget areas and ordered placements |
| Services / testimonials / comments | No corresponding complete CMS modules found in the inspected foundation | Add as separate modules where required by the approved CMS scope; do not hard-code their frontend content |
| Analytics / activity | No event/stat tables or activity log found | Add lightweight page-view aggregation and admin activity log |

## 3. Database architecture

### 3.1 Preserve and extend existing tables

Do not drop or recreate the existing tables. Keep existing IDs, foreign keys, slugs, media paths and records.

**Existing tables to retain:** `users`, `roles`, `permissions`, `role_user`, `permission_role`, `categories`, `properties`, `property_images`, `posts`, `categoryables`, `pages`, `media`, `menus`, `menu_items`, `banners`, `team_members`, `enquiries`, `settings`.

**Additive changes:**

- `properties`: add `cta_mode` (default `contact_form` for backwards compatibility), nullable `whatsapp_number`, nullable `whatsapp_message`; optionally `cta_label`. The existing global WhatsApp setting remains a fallback only when the property's own value is absent and its CTA mode allows WhatsApp.
- `menu_items`: add `link_type` (route, property, page, post, category, external, custom), nullable `route_name`, nullable `entity_type` / `entity_id`, nullable `icon` and `css_class`. Retain `url` as the custom/external URL and retain existing `parent_id`, `target`, `sort_order`, `is_active`.
- `pages`: add optional structured page-builder metadata only if the page editor requires it; keep existing `content` and `template` as a compatibility path.
- `enquiries`: retain current `new` default and current fields; add assignment and source metadata only in a later additive migration if the workflow requires them.

### 3.2 New tables

Every new table gets its own timestamped migration. Use foreign keys, indexes, timestamps, and soft deletes where lifecycle/history requires them.

| Table | Purpose and principal fields |
|---|---|
| `homepages` | Named homepage documents: `id, name, slug, design_key, status, is_default, meta_title, meta_description, published_at, created_by, updated_by`. `design_key` accepts the ten source designs: `index, home-2, …, home-10`. Unique slug; only one published default homepage enforced in application transaction. |
| `homepage_sections` | Ordered instances on a homepage: `id, homepage_id, section_key, section_type, source_design, reusable_section_id nullable, sort_order, is_enabled, config JSON, data_source JSON nullable`. Index by homepage/order and unique instance key. Each row is one draggable section instance. |
| `reusable_sections` | Saved reusable section presets: `id, name, slug, section_type, config JSON, status, created_by, updated_by`. A homepage section may reference a preset but can override its instance config. |
| `widgets` | Widget instances: `id, name, type, config JSON, status, created_by, updated_by`. Widget types are registered in PHP; arbitrary executable code is never stored in the database. |
| `widget_areas` | Named placement regions such as header utility, sidebar, footer columns: `id, name, location unique, description, is_active`. |
| `widget_area_items` | Ordered widget placement: `id, widget_area_id, widget_id, sort_order, is_active`; unique placement/order as appropriate. |
| `page_view_events` | Minimal analytics event: `id, path, route_name nullable, page_type nullable, entity_id nullable, referrer_host nullable, visitor_hash nullable, session_hash nullable, occurred_at`. Index date/path and route/date; do not store raw IP or full query strings. |
| `daily_page_stats` | Aggregated reporting: `id, stat_date, path, page_type nullable, views, unique_visitors`; unique date/path. Can be populated by scheduled aggregation or incremented safely. |
| `admin_activities` | Admin audit trail: `id, user_id nullable, action, subject_type nullable, subject_id nullable, description, metadata JSON nullable, created_at`. Index user/date and subject. |
| `services` | Editable services content: title, slug, excerpt/content, icon/image, order, status, SEO fields. |
| `testimonials` | Editable client feedback: name, role/company, quote, photo, rating, order, status. |
| `comments` | Moderated content comments if comments are in the approved scope: polymorphic subject, author/contact, body, status, moderation metadata. |

### 3.3 Data integrity rules

- Use JSON only for genuinely variable section/widget configuration; keep queryable business fields in columns.
- Never delete existing data to install the builder. All schema evolution is additive.
- Keep the current property enquiry path functional during CTA rollout.
- Use DB transactions for homepage section reorder/save, menu tree save, and publish/default changes.
- Validate JSON against the registered section/widget schema before persistence.
- Use explicit status values and publish timestamps; drafts must never leak to public routes.
- Cache published homepage/menu/settings data and invalidate only the affected keys after successful writes.

## 4. Proposed Laravel structure

Keep existing namespaces and files where possible. Add cohesive folders rather than duplicating existing modules.

```text
app/
  Actions/
    Homepage/SaveHomepage.php
    Homepage/PublishHomepage.php
    Menus/SyncMenuTree.php
  Data/
    Homepage/SectionData.php
  Http/
    Controllers/
      Admin/
        HomepageController.php
        HomepageSectionController.php
        ReusableSectionController.php
        WidgetController.php
        WidgetAreaController.php
        AnalyticsController.php
        ActivityController.php
      Frontend/
        HomeController.php
    Requests/
      Admin/
        StoreHomepageRequest.php
        UpdateHomepageRequest.php
        SyncHomepageSectionsRequest.php
        StoreWidgetRequest.php
        SyncMenuRequest.php
  Models/
    Homepage.php
    HomepageSection.php
    ReusableSection.php
    Widget.php
    WidgetArea.php
    WidgetAreaItem.php
    PageViewEvent.php
    DailyPageStat.php
    AdminActivity.php
    Service.php
    Testimonial.php
  Services/
    Homepage/
      SectionRegistry.php
      HomepageRenderer.php
      SectionDataResolver.php
    Widgets/
      WidgetRegistry.php
      WidgetRenderer.php
    Navigation/
      MenuTreeBuilder.php
    Analytics/
      PageViewRecorder.php
  View/
    Components/
      Frontend/
        Sections/
          Hero*.php (only where component classes add value)
resources/views/
  admin/
    homepages/
      index.blade.php
      form.blade.php
      builder.blade.php
      partials/
    reusable-sections/
    widgets/
    analytics/
  frontend/
    home.blade.php
    sections/
      [registered section partials]
    components/
      property-card.blade.php
      post-card.blade.php
      team-card.blade.php
      testimonial-card.blade.php
    theme-pages/
      home-2.blade.php ... home-10.blade.php
routes/
  web.php
  admin.php
database/
  migrations/
  seeders/
    HomepageTemplateSeeder.php
    WidgetAreaSeeder.php
config/
  cms-sections.php
  cms-widgets.php
```

Do not move or rename existing files just for aesthetic consistency. This is the target organization for new work and gradual extraction.

## 5. Route architecture

### Public routes — `routes/web.php`

- `GET /` — resolve the published default homepage and render its ordered section instances.
- `GET /properties`, `GET /properties/{property:slug}` — preserve existing property routes.
- `POST /enquiries` — preserve and throttle existing enquiry submission.
- `GET /blog`, `GET /blog/{post:slug}` — preserve existing blog routes.
- `GET /{page:slug}` — published CMS pages, with reserved route names/paths protected from collision.
- `POST /newsletter` — preserve existing newsletter endpoint.
- Keep `/{template}.html` compatibility routes for all original theme pages, including `home-2` through `home-10`; these preview/source routes must not be mistaken for the live homepage selection.

### Admin routes — `routes/admin.php`

Register this file through Laravel's route bootstrap only after verifying the project's Laravel 13 routing bootstrap. Prefix `admin`, name `admin.`, and apply existing `auth` and `admin.access` middleware.

- Existing resources remain: properties, enquiries, content, media, menus, roles, users, newsletters, settings, SEO, appearance.
- Add: `homepages`, homepage builder section sync/reorder, publish/default actions, reusable sections, widgets, widget areas, analytics, activity log.
- Use resource controllers and Form Requests; keep authorization permissions on each route/action.
- Avoid a broad catch-all route before named resources.

## 6. Homepage builder architecture

### 6.1 Ten original designs are first-class templates

Keep all ten original HTML files unchanged in `theme-files-html/`. They are the visual/design source of truth. Keep the existing converted Blade pages and correct them against the originals; do not replace all ten with one generic layout.

A homepage record selects a starting design (`index`, `home-2` … `home-10`). It is a starting point, not a lock-in: users can combine compatible sections from the complete section catalog across all ten designs.

### 6.2 Section catalog and renderer

- Maintain a code-owned section registry in `config/cms-sections.php` (or a dedicated registry class). Each definition includes a stable key, human label, source design(s), Blade partial, configuration schema/defaults, supported data source, preview metadata and compatibility notes.
- Extract each meaningful source section into a reusable Blade partial while preserving its original markup, CSS classes, responsive structure and theme assets.
- The registry contains the union of sections found in all ten designs: hero/search variants, category/property-type grids, property listings, value/process/about, services, projects, stats, team, testimonials, blog, newsletter/CTA, and design-specific sections. The exact catalog is built from source HTML, not guessed from names.
- Render via a single `HomepageRenderer` that loads published homepage sections ordered by `sort_order`, resolves data through `SectionDataResolver`, and includes the registered partial. Unknown section keys fail safely and are visible in admin diagnostics; never execute a view path supplied directly by the database.
- Section configuration stores editable copy, images/media IDs, buttons, colors only where the source design permits them, layout variants, visibility and data-source options. Dynamic property/post/team/testimonial cards query their existing models.
- All visible text, image, CTA, links, counts, cards and section visibility must come from a setting, content record, section config or registered data provider. The original static text is used only as a one-time migration/seed default, not as an uneditable permanent frontend source.

### 6.3 Persistent drag-and-drop

- Builder UI has a section library grouped by source design/type, a canvas reflecting the current homepage, and a settings panel for the selected section.
- Use the existing Bootstrap/jQuery stack and a no-build drag-and-drop implementation (native HTML5 DnD or a vendored dependency already present; do not introduce Node tooling).
- Reordering, adding, duplicating, enabling/disabling and removing an instance persists through a throttled/explicit save endpoint.
- The server validates section keys/configuration, checks authorization, and writes the complete order in a transaction. Client-only order is never treated as saved.
- Provide draft preview, publish, restore-to-template-defaults and safe confirmation for destructive actions. Publishing a homepage and setting it as default are separate authorized operations.

### 6.4 Ten design preservation and composition

- `index.html` maps to the primary `home.blade.php`/index design.
- `home-2.html` through `home-10.html` remain separately accessible and serve as design presets/previews.
- Every meaningful section from every source design is represented in the catalog. A homepage can combine sections from multiple source designs; each instance retains its originating design key for accurate markup and styling.
- Keep design-specific body classes/header/footer variants where needed. A section must not silently lose its source-specific classes or required assets when moved to another homepage.

## 7. Reusable section and widget architecture

### Reusable sections

A reusable section is a saved configuration/section composition, not copied executable Blade. It has a type/key, title, config JSON, status and audit metadata. It can be inserted into multiple homepages. Each insertion creates a `homepage_sections` instance that references the preset and stores optional local overrides, so editing one page does not unexpectedly mutate every other page unless the admin explicitly updates the shared preset.

### Widgets

Widget types are PHP-registered and include settings schema, validation, renderer and supported placement rules. Initial types should be driven by actual theme needs: contact details, social links, recent properties, featured properties, recent posts, newsletter, office hours and custom navigation. The database stores only widget instance configuration. Widget areas define stable theme locations; ordered area items place instances. No PHP, Blade, JavaScript or raw executable code is accepted from admin widget settings.

## 8. Navigation menu architecture

- Retain existing `menus` and `menu_items` records.
- Add a typed link resolver: route, CMS page, property, post, category, external URL or custom URL.
- Admin UI provides searchable selectors for internal entities and route names, plus URL/target, icon, CSS class, active state and parent.
- Persist tree updates transactionally. Reject self-parenting, cycles, cross-menu parent references and excessive depth (configurable safe maximum, not a hard-coded five-level query).
- Render recursively from an in-memory tree or recursive relationship, with active URL state and accessible dropdown markup.
- Cache by menu location and invalidate on every item/menu mutation.
- Preserve old URL-only items as `custom` links during migration.

## 9. Property CTA workflow

1. Admin edits a property and selects CTA mode: `contact_form` or `whatsapp` (a future `both` mode can be enabled only if the approved UX calls for it).
2. For WhatsApp mode, validate the per-property number in international digits format and allow a property-specific message template. If no property number is supplied, use the global WhatsApp number only as an explicit fallback.
3. For contact-form mode, show the existing enquiry form and continue posting to the existing throttled `enquiries.store` route; preserve property_id and existing enquiry storage.
4. WhatsApp mode shows a safe external `wa.me` link with URL-encoded message and `rel="noopener noreferrer"`; it does not create a fake enquiry record.
5. If configuration is incomplete, show a safe fallback to the existing contact form rather than rendering a dead CTA.
6. Keep CTA mode and number visible in admin lists/forms, and test all paths before considering the feature complete.

## 10. Implementation phases and dependencies

| Phase | Deliverables | Dependencies / exit criteria |
|---|---|---|
| 0 — Architecture baseline | This audit/plan, preserve-data rules, route/schema/renderer decisions | Approved architecture; no production data changes |
| 1 — Safe foundations | Format existing code touched; split route registration safely; add one-table-per-migration additive schema for builder/CTA; models, relationships, requests, policies | Verify Laravel 13 bootstrap, existing migrations and current branch head; migrations must be additive and reversible |
| 2 — Ten-template fidelity | Compare all ten source HTML pages with their Blade counterparts; verify asset paths, CSS/JS/fonts/icons/images; fix missing markup and responsive behavior without changing original design | Phase 1 route/bootstrap and asset mapping stable |
| 3 — Homepage CMS data layer | Homepage/reusable-section models, registry, data resolver, seeder that registers ten designs and section catalog | Phase 1 schema; Phase 2 source inventory |
| 4 — Homepage builder UI | Section library, settings panel, persistent drag/drop, add/remove/duplicate/enable, draft preview, publish/default | Phase 3 registry and persistence |
| 5 — Dynamic frontend rendering | Render any ordered combination of sections from all ten designs; connect all visible content to config/records; retain static theme preview routes | Phase 3 and 4; no section may render unregistered DB view paths |
| 6 — Existing module upgrades | Property CTA; menu entity links and recursive rendering; media picker; page/post/category/service/team/testimonial content integration | Phase 1 schema; existing module regression checks |
| 7 — Widgets | Registry, widget instances/areas, admin placement and frontend renderers | Phase 3 section/data provider patterns |
| 8 — Analytics and activity | Page view recorder, aggregation, admin activity, dashboard charts/tables | Phase 1 tables; privacy and retention settings |
| 9 — Quality/security/hardening | Form Request coverage, permission audit, output escaping, upload validation, caching, query review, accessibility, responsive visual regression | All functional phases |
| 10 — Deployment verification | Run migrations/seeders/tests, route/view cache, manual browser checks, deployment and rollback notes | Working PHP/Composer/MySQL/browser environment |

### Working rules

- Work only on `feat/property-cms-foundation`; do not create a branch or duplicate PR.
- Before each write, fetch the current file/blob SHA and target branch head where available. Never overwrite a changed file based on stale content.
- Prefer additive migrations and targeted edits. Do not delete/recreate existing modules or rewrite original theme source.
- Keep code readable and PSR-12 formatted; avoid one-line controllers/routes in new code.
- Each implementation phase ends with a change summary, exact files changed, verification performed, and limitations. Tests not executed are reported as not run.
- No PR is created unless the user explicitly asks for one or an existing PR needs its description updated.


## 11. Implementation status on `feat/property-cms-foundation`

This status reflects GitHub-side source changes only; it is not a claim that the application has been executed.

### Implemented in the current work

- Added the architecture plan and a separate ten-homepage theme fidelity inventory.
- Added additive, table-specific migrations for `homepages`, `reusable_sections`, `homepage_sections`, `widgets`, `widget_areas`, `widget_area_items`, and per-property CTA fields.
- Added Eloquent models for homepage composition, reusable sections and widget placements.
- Added an idempotent seeder for all ten original homepage design records.
- Added homepage selection, draft-copy, publish and default-selection workflows.
- Added a first section catalog: hero variants from all ten designs, plus database-driven property, team and blog sections from the primary design.
- Extracted the ten hero blocks into registered Blade partials. The primary hero uses the existing banner records; the other hero headings support per-instance builder overrides.
- Added a draft-only section builder with add, remove, enable/disable, editable heading fields, and server-persisted HTML5 drag-and-drop ordering.
- Added frontend rendering of the saved section order using a code-owned view registry; database values cannot choose arbitrary Blade paths.
- Added per-property CTA mode and WhatsApp fields. The default is `both` to preserve the existing contact form and global WhatsApp action; an unavailable WhatsApp number safely falls back to the enquiry form.
- Split admin routes from public routes and moved the catch-all CMS page route after admin route registration to avoid route shadowing.

### Still in progress

- Extract and register the remaining non-hero sections from all ten original designs; the initial section catalog is intentionally not presented as the complete final catalog.
- Replace all remaining static headings, body copy, image selections, counters, links, testimonials, service cards, project cards, FAQ items and footer/header content with CMS records or section settings.
- Build the full widget administration and widget-area placement interface.
- Upgrade menu items with typed internal entity/route selectors, cycle-safe tree editing and recursive rendering.
- Add analytics, page-view aggregation and admin activity history.
- Add service/testimonial/comment modules where required by the final approved scope.
- Complete desktop/tablet/mobile side-by-side browser verification against all ten original HTML files.

### Verification status

No Composer install, Artisan command, migration, seeder, PHPUnit/Pest test, route-list check or browser screenshot was executed in this environment. The GitHub connection accepted the source changes, but runtime behavior and database compatibility remain unverified until the branch is run against the project's Laravel/MySQL environment.


### Homepage builder follow-up — 2026-11-17

The latest source changes extend the initial builder foundation:

- The section registry now defines typed, per-section field schemas. The admin form renders text, textarea, URL/path, color, number, checkbox and select controls from each section's schema. Server validation whitelists those fields and merges partial updates.
- The primary property, team and blog sections now expose additional database-backed display controls, including bounded item counts and supported filters/metadata visibility.
- Section instances can be duplicated. Drafts have an authenticated preview route.
- Reusable sections can be saved from a homepage instance and inserted into another draft. The new additive migration stores stable `section_key` and `source_design` values; instances reference a shared library row and may store local overrides.
- A live-safety flag, `homepages.is_composition_complete`, prevents incomplete section compositions from replacing the complete original homepage. The frontend falls back to the original full template unless the flag is true, and admin default-selection is blocked until completion is explicitly verified.
- The fidelity audit now lists all 57 identified literal `<section>` wrappers by design, with source data expectations and extraction/admin/dynamic/responsive gates. Hero and div-based blocks are listed separately as further inventory work.

**Important completion status:** this is still an incomplete CMS phase. Thirteen registered partials exist (ten heroes plus property/team/blog), but none has passed the full strict completion gate for all visible fields, images, links, layout options, interactions and responsive comparison. The 57-wrapper inventory is not the final count of every content block because many source blocks are div-based. The section catalog is not yet complete, and secondary-template drafts still begin with only the registered hero section when no saved composition exists. Do not set `is_composition_complete` to true until the audit gates are completed for that design.

**Runtime verification:** PHP CLI is present in the assistant execution environment, but the repository is not checked out there and Composer/vendor dependencies, application database and browser are not available. No Laravel commands, migrations, seeders, automated tests, route compilation or browser comparisons have been run. This remains source-level implementation only.


### Homepage section extraction follow-up — 2026-11-17

The section registry now composes dedicated files from `config/cms-sections/` for category sliders, Home 2 about composition and Home 2 property values. A `SectionDataResolver` service supplies section-specific category records/counts; section views remain small, source-family-specific Blade renderers. The Home 2 about composition and property-values gallery expose their text, images, icons, links and repeated feature/card content through their own schemas. Home 2 draft initialization now includes hero, categories and about in source order; the remaining intervening source blocks must be registered before the full initial sequence is complete.

The existing Media Library now has a permission-protected image picker endpoint and the homepage builder exposes a searchable image selection modal for `image` fields. Category content editing supports category icon upload, sort order and active state. This picker stores the selected Media Library URL in section configuration; a future hardening pass should consider normalized media IDs/relationships and storage URL changes.

**Still not complete:** source blocks across all ten pages remain largely unextracted, section fields and interactions are not yet exhaustively mapped, responsive visual regression is pending, and the strict per-design completion gate remains closed. No Laravel runtime or browser verification was available during this source-only implementation pass.


The Home 2 section library has expanded to include its source-specific team carousel, property-values gallery and neighborhood gallery. Team and category records are resolved by `SectionDataResolver`; the remaining about/gallery content is stored in per-instance configuration. The team editor accepts validated social-link JSON. The image picker is used by all section fields typed as `image`, including hero, category, about, gallery, property-values and team decorative media. All of these remain pending Laravel/browser execution and responsive screenshot comparison.


### Home 2 major content sequence — 2026-11-17

The Home 2 preset now has 15 major source blocks represented as registered builder sections in the source order: hero, advanced search, categories, about, benefits, published-property grid, neighborhood gallery, property values, team carousel, app-download/QR showcase, marquee, second featured-property carousel, testimonials/trust summary, FAQ accordion and blog carousel. The Home 2 draft initializer seeds these 15 sections. The hero and advanced search are separate builder sections but their controls are connected using the HTML form association, so location/min-area/max-area filters submit with the hero's existing property search form and match the current property index query parameters.

Section definitions are split across dedicated files under `config/cms-sections/`; rendering is handled by section-family Blade views and `SectionDataResolver` providers. The Media Library picker is available for image-typed fields. This is a source-level milestone, not a production sign-off: favorites persistence and blog comment counts are not implemented, shared header/footer/global search still need their own complete inventory, and the full field/interaction plus responsive screenshot audits are pending. The strict ten-template completion gate remains closed until all ten designs pass those checks.
