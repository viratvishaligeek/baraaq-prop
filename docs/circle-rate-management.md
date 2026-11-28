# Bareilly Property Circle Rate Management

## Installation and initial data

Run the normal project migration and seeding commands:

```bash
php artisan migrate
php artisan db:seed
php artisan test
```

`CircleRateReferenceSeeder` creates only configurable reference data: Bareilly district, the six tehsils listed by the Bareilly district directory, fifteen development-block locations, Urban/Rural area types, and optional location-level types. It does not create circle-rate amounts, property categories, property types, or valuation assumptions. It uses `firstOrCreate` so later seeder runs do not overwrite administrator edits.

The official district directory reports 2,070 villages. A complete village/mohalla list is not fabricated or seeded; use the staged Bulk Location Import after obtaining an authoritative dataset. Block-to-tehsil mappings are left unset because the cited directory does not provide those relationships.

## Admin workflow

1. Create/verify district and tehsil master data.
2. Configure area types, location levels, locations, property types, categories, rate units and conversions, and road-width ranges.
3. Create financial years and select the current/frontend-visible year.
4. Configure calculator fields and any source-backed calculation rules.
5. Register the official circle-rate schedule as a source document or official URL.
6. Upload rates through Excel/CSV Import or enter them manually.
7. Review validation/conflicts. Imported rates are drafts and never auto-published.
8. Verify and publish rates only after source review. Editing a published rate creates a version and returns the changed record to draft/pending review.

The public calculator only considers published, verified rates with an attached source document or official URL. If no unique rate matches, it returns an actionable error rather than estimating from a nearby or generic value.

## Rate import columns

The importer supports automatic header detection and manual mapping. Common fields include:

- District, Tehsil, Area Type, Location
- Property Type, Category, Road Width
- Rate, Unit, Financial Year
- Effective From, Effective Until
- Conditions (JSON list)

Example conditions:

```json
[
  {"field":"area","operator":"between","value":[100,250]},
  {"field":"custom.land_use","operator":"equals","value":"residential"}
]
```

Existing verified rates are never overwritten. An explicit confirmation option may update one matching unverified record; the old record is first snapshotted in rate history. Conflicting or ambiguous matches remain for manual review.

## Bulk location import columns

Required: District and Name.

Optional: Tehsil, Area Type, Location Level, Parent Location Name, Slug, Location Code.

Parent locations must already exist or appear earlier in the file. Duplicate locations are flagged and not overwritten. Administrators should map blocks to tehsils only after verifying the official administrative relationship.

## Custom valuation formulas

Property types can use a supported built-in area strategy or a safe administrator-defined arithmetic formula. Formula configuration is JSON, for example:

```json
{
  "formula": "converted_area * rate * (1 - custom.depreciation_percent / 100)"
}
```

Available variables:

- `area` / `converted_area`: area converted to the rate unit
- `input_area`: user-entered area before conversion
- `rate`: selected verified rate
- `custom.field_name`: numeric value from an applicable custom calculator field

Only `+`, `-`, `*`, `/`, and parentheses are supported. Function calls, PHP expressions and executable code are rejected. Numeric custom fields such as age, floors, construction area or depreciation percentage can be used in formulas and rule conditions. A custom formula must be configured before a custom strategy can be used.

Rule-based multipliers and fixed adjustments are applied only from active, verified rules with a source document or official URL. Unit conversions must be configured and verified; no universal Bigha conversion is assumed.

## Reports, audit and API

- Valuation records retain the selected rate, source, formula, applied conditions and previous-year comparison.
- Admin CSV exports sanitize spreadsheet formula prefixes.
- Source changes, rate changes, imports and master-data changes are audit logged.
- API credentials are encrypted. No undocumented government endpoint is assumed, scraped or called. Configure a documented official API contract before any synchronization is enabled.
- Result pages include a print stylesheet so users can print or save as PDF through the browser.

## Verification

The feature test suite covers rate resolution, ambiguity, historical comparison, unit conversion, verified rules, custom formulas, formula injection rejection, CSV parsing and reference-seeder idempotency. The repository CI workflow is configured for PHP 8.4 and SQLite. A green CI result and end-to-end browser/database verification are still required before merge or public activation.
