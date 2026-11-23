# Production-safe database migration strategy

## Scope reviewed

The current branch contains the original Laravel framework migrations and the CMS migrations dated 17–23 November 2026. The review covered the initial CMS schema, homepage and section tables, media attachments, dynamic menu links, content metadata, page/service section ownership, widget compatibility tables, the fixed-homepage uniqueness migration, and the optional enquiry-phone migration.

## Findings

- The CMS baseline migration creates the main content, media, menu, settings, and enquiry tables. It is a historical bootstrap migration and must not be edited after it has been applied to any shared environment.
- The homepage-section migration initially creates a required homepage_id; the later page-generalization migration makes it nullable and adds page_id. Its rollback deliberately refuses to restore the non-null constraint while page-owned or null-homepage rows remain.
- The fixed-homepage schema migration checks for duplicate (homepage_id, section_key) pairs before adding the unique index. This preflight is appropriate; resolve duplicate data explicitly rather than deleting rows automatically.
- The legacy-builder cleanup migration is intentionally a no-op. It preserves legacy widgets, widget areas, reusable sections, and their authored data. Keep this retention behavior until a separate export, retention, and dependency review authorizes physical deletion.
- The enquiry-phone migration changes the existing column to nullable. Its rollback converts null values to empty strings before restoring NOT NULL; this is a deliberate data transformation and should not be treated as a lossless rollback.
- The older migrations include table/column renames and change() operations. These should be tested against the actual production database engine and version, not assumed equivalent from SQLite tests.

## Required production strategy

1. Treat every migration already deployed to a shared environment as immutable. Correct mistakes with a new forward migration.
2. Prefer additive, nullable columns and indexes first. Deploy application code that can read both old and new representations before any later backfill or constraint tightening.
3. Before adding unique constraints, run duplicate and nullability preflight queries and produce a reviewed reconciliation report. Never auto-delete administrator-authored rows in a migration.
4. Before changing a column type or nullability, measure table size, inspect existing null/invalid values, verify engine-specific locking behavior, and rehearse on a production-sized copy.
5. Keep data cleanup and physical table drops in a separate release after backups, dependency searches, retention approval, and a verified export. Do not combine them with feature migrations.
6. Deploy with a verified database backup, a staging migration rehearsal, a migrate --pretend review, and a reviewed migrate --force execution during the approved deployment window.
7. After deployment, verify migration status, critical row counts, foreign-key integrity, homepage section uniqueness, settings values, media attachment counts, and the primary public/admin flows.
8. Do not rely on rolling back destructive or data-transforming migrations as the recovery plan. Restore from the verified backup or ship a reviewed forward-fix migration.

## Current implementation boundary

No historical migration was rewritten and no schema was changed in this code pass. The current fixes are application-level: persisted Homepage section configuration now takes precedence over legacy data_source values, and the Home 9 header now resolves the configured menu location. Database runtime status and production row contents remain unverified because no database connection or executable checkout was available through the repository file API.

## Seeder safety

The default DatabaseSeeder now runs PillerDemoContentSeeder automatically only in local/testing environments. On other environments, demo records require an explicit SEED_DEMO_CONTENT=true opt-in. Keep production owner data out of ordinary database seeding; the role/permission, homepage-template, and default-theme seeders remain the baseline setup seeders.
